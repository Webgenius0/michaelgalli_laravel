<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Models\MealPlanOption;
use App\Models\SubscriptionFeature;
use App\Models\UserFamilyCart;
use App\Models\UserPlanCart;
use App\Models\UserRecipeCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    // meal plan
    public function mealPlans(Request $request)
    {

        $plan_id = $request->plan_id ?? 0;

        $mealPlans = MealPlan::with('options')
            ->when($plan_id > 0, function ($q) use ($plan_id) {
                $q->where('id', $plan_id);
            })
            ->get();

        if ($mealPlans->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No meal plans available.',
                'data'    => [],
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Meal plans retrieved successfully.',
            'data'    => $mealPlans,
        ], 200);
    }

    public function package_select(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meal_plan_id'        => 'required|exists:meal_plans,id',
            'meal_plan_option_id' => 'required|exists:meal_plan_options,id',

            'member_ids'          => 'nullable|array',
            'member_ids'          => 'nullable|exists:user_family_members,id',

            // 'recipe_ids'          => 'required|array',
            // 'recipe_ids'          => 'required|exists:recipes,id',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'data'    => [],
            ], 422);
        }

        $meal_plan = MealPlan::find($request->meal_plan_id);
        if (! $meal_plan) {
            return response()->json([
                'status'  => false,
                'message' => 'Meal plan not found',
                'data'    => [],
            ], 404);
        }
        $meal_plan_option = MealPlanOption::find($request->meal_plan_option_id);

        if (! $meal_plan_option) {
            return response()->json([
                'status'  => false,
                'message' => 'Meal plan option not found',
                'data'    => [],
            ], 404);
        }

        $user = auth('api')->user();

        // Check if user already has recipes in their cart
        $hasRecipes = UserRecipeCart::where('user_id', $user->id)->exists();

        // If recipes exist, delete them
        if ($hasRecipes) {
            UserRecipeCart::where('user_id', $user->id)->delete();
        }

        $total_servings = $meal_plan->people_count * $meal_plan_option->recipes_per_week;
        $total_price    = $total_servings * $meal_plan_option->price_per_serving;

        // user plan cart table store data create or update
        $cart = UserPlanCart::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'meal_plan_id'        => $meal_plan->id,
                'meal_plan_option_id' => $meal_plan_option->id,
                'price_per_serving'   => $meal_plan_option->price_per_serving,
                'people_count'        => $meal_plan->people_count,
                'recipes_per_week'    => $meal_plan_option->recipes_per_week,
                'total_servings'      => $total_servings,
                'total_price'         => $total_price,
                'status'              => 'pending',
            ]
        );

        $family_cart = [];
        // user family cart create or update
        if (is_array($request->member_ids)) {
            foreach ($request->member_ids as $member_id) {
                $family_cart[] = UserFamilyCart::updateOrCreate(
                    [
                        'user_id'               => $user->id,
                        'user_family_member_id' => $member_id,
                    ]
                );
            }
        }

        // user recipee cart create and update
        // if (is_array($request->recipe_ids)) {
        //     foreach ($request->recipe_ids as $recipe_id) {
        //         UserRecipeCart::updateOrCreate(
        //             [
        //                 'user_id'   => $user->id,
        //                 'recipe_id' => $recipe_id,
        //             ]
        //         );
        //     }
        // }

        return response()->json([
            'status'  => true,
            'message' => 'Recipe addded to cart successfully.',
            'data'    => [
                'cart'        => $cart,
                'family_cart' => $family_cart,

            ],
        ], 200);

    }

    public function selected_package(Request $request)
    {
        $user = auth('api')->user();

        // Fetch cart data
        $cart = UserPlanCart::where('user_id', $user->id)->first();
        if (! $cart) {
            return response()->json([
                'status'  => false,
                'message' => 'No cart found for user.',
                'data'    => [],
            ], 404);
        }

        $meal_plan = MealPlan::find($cart->meal_plan_id);
        if (! $meal_plan) {
            return response()->json([
                'status'  => false,
                'message' => 'Meal plan not found.',
                'data'    => [],
            ], 404);
        }

        // Get selected members
        $selected_members = UserFamilyCart::where('user_id', $user->id)->get();

        // Get selected recipes with details (name, image, etc.)
        $selected_recipes = UserRecipeCart::where('user_id', $user->id)
            ->with('recipe:id,title,image_url') // eager load only necessary fields
            ->get()
            ->map(function ($item) {
                return [
                    'id'    => $item->recipe->id,
                    'title'  => $item->recipe->title,
                    'image_url' => url($item->recipe->image_url),
                ];
            });

        if ($selected_members->isEmpty() || $selected_recipes->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart must have at least one member and one recipe.',
                'data'    => [],
            ], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Checkout session created successfully.',
            'data'    => [
                'plan'             => $cart,
                'selected_members' => $selected_members,
                'selected_recipes' => $selected_recipes,
            ],
        ], 200);
    }

    public function add_to_cart(Request $request)
    {

        $request->validate([
            'recipe_id' => 'required|integer|exists:recipes,id',
        ]);

        $user = auth('api')->user();

        // Get user plan and recipe limit
        $user_plan = UserPlanCart::where('user_id', $user->id)->first();

        if (! $user_plan) {
            return response()->json([
                'status'  => false,
                'message' => 'No active meal plan found.',
            ], 400);
        }

        $recipe_limit = $user_plan->recipes_per_week ?? 0;

        // Count already added recipes for the week
        $already_added = UserRecipeCart::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Check if already exists
        $already_exists = UserRecipeCart::where('user_id', $user->id)
            ->where('recipe_id', $request->recipe_id)
            ->exists();

        // if ($already_exists) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'This recipe is already in your cart.',
        //     ], 409);
        // }

        // Check limit
        if ($already_added >= $recipe_limit) {
            return response()->json([
                'status'  => false,
                'message' => "You have reached your weekly recipe limit of {$recipe_limit}.",
                'data'    => [],
            ], 403);
        }

        // Add recipe
        $recipe_cart = UserRecipeCart::create([
            'user_id'   => $user->id,
            'recipe_id' => $request->recipe_id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Recipe added to cart successfully.',
            'data'    => $recipe_cart,
        ]);

    }

    public function remove_to_cart(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'recipe_id' => 'required|integer|exists:user_recipe_carts,recipe_id',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'data'    => [],
            ], 422);
        }

        $user = auth('api')->user();

        // Find and delete the recipe from the user's cart
        $deleted = UserRecipeCart::where('user_id', $user->id)
            ->where('recipe_id', $request->recipe_id)
            ->first();
        $deleted->delete();

        if ($deleted) {
            return response()->json([
                'status'  => true,
                'message' => 'Recipe removed from cart successfully.',
                'data'    => $deleted,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Recipe not found in your cart.',
        ], 404);

    }

    public function subscribe(Request $request)
    {
        $user = auth('api')->user();

        // Fetch cart data
        $cart = UserPlanCart::where('user_id', $user->id)->first();
        if (! $cart) {
            return response()->json([
                'status'  => false,
                'message' => 'No cart found for user.',
                'data'    => [],
            ], 404);
        }

        $meal_plan = MealPlan::find($cart->meal_plan_id);
        if (! $meal_plan) {
            return response()->json([
                'status'  => false,
                'message' => 'Meal plan not found .',
                'data'    => [],
            ], 404);
        }

        // Get member_ids from UserFamilyCart
        $member_ids = UserFamilyCart::where('user_id', $user->id)->pluck('user_family_member_id')->toArray();

        // Get recipe_ids from UserRecipeCart
        $recipe_ids = UserRecipeCart::where('user_id', $user->id)->pluck('recipe_id')->toArray();

        if (empty($member_ids) || empty($recipe_ids)) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart must have at least one member and one recipe.',
                'data'    => [],
            ], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $checkoutSession = Session::create([
                'line_items'  => [[
                    'price_data' => [
                        'currency'     => 'aed',                            // Or use $meal_plan->currency if available
                        'unit_amount'  => intval($cart->total_price * 100), // Stripe expects amount in cents
                        'product_data' => [
                            'name' => $meal_plan->name,
                        ],
                        'recurring'    => [
                            'interval' => $meal_plan->billing_cycle ?? 'week', // 'week', 'month', etc.
                        ],
                    ],
                    'quantity'   => 1,
                ]],
                'mode'        => 'subscription',
                'success_url' => 'https://nutri-craft.netlify.app/payment-success',
                'cancel_url'  => 'https://nutri-craft.netlify.app/payment-cancel',
                'metadata'    => [
                    'user_id'      => auth('api')->id(),
                    'meal_plan_id' => $meal_plan->id,
                    'member_ids'   => implode(',', $member_ids ?? []),
                    'recipe_ids'   => implode(',', $recipe_ids), // Example: [5, 9, 13]
                ],
            ]);

            // dd($checkoutSession);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Error creating checkout session: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        } catch (IncompletePayment $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Payment incomplete: ' . $e->getMessage(),
                'data'    => [],
            ], 400);
        }

        $checkout_url = $checkoutSession->url;

        return response()->json([
            'status'  => true,
            'message' => 'Checkout session created successfully.',
            'data'    => [
                'checkout_url' => $checkout_url,
            ],
        ], 200);
    }

    public function success($user_id)
    {
        // Handle successful subscription logic here
        return response()->json([
            'status'  => true,
            'message' => 'Subscription successful for user ID: ' . $user_id,
            'data'    => [],
        ], 200);
    }

    public function cancel($user_id)
    {
        // Handle subscription cancellation logic here
        return response()->json([
            'status'  => false,
            'message' => 'Subscription cancelled for user ID: ' . $user_id,
            'data'    => [],
        ], 200);
    }

    // pause subscription
    public function pauseSubscription(Request $request)
    {
        $user         = auth('api')->user();
        $subscription = $user->subscriptions()->where('stripe_status', 'active')->first();

        if (! $subscription) {
            return response()->json([
                'status'  => false,
                'message' => 'No active subscription found.',
                'data'    => [],
            ], 404);
        }

        // Stripe API call to pause subscription
        $stripeSub = $this->stripe->subscriptions->update($subscription->stripe_subscription_id, [
            'pause_collection' => ['behavior' => 'keep_as_draft'],
        ]);

        // Update local DB
        $subscription->stripe_status = 'paused';
        $subscription->save();

        return response()->json([
            'status'  => true,
            'message' => 'Subscription paused successfully.',
            'data'    => $stripeSub,
        ], 200);
    }

    // cancel subscription
    public function cancelSubscription(Request $request)
    {
        $user         = auth('api')->user();
        $subscription = $user->subscriptions()->where('stripe_status', 'active')->first();

        if (! $subscription) {
            return response()->json([
                'status'  => false,
                'message' => 'No active subscription found.',
                'data'    => [],
            ], 404);
        }

        // Stripe API call to cancel subscription
        $stripeSub = $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);

        // Update local DB
        $subscription->stripe_status = 'canceled';
        $subscription->save();

        return response()->json([
            'status'  => true,
            'message' => 'Subscription cancelled successfully.',
            'data'    => $stripeSub,
        ], 200);

    }

    public function subscriptionDetails()
    {
        $user         = auth('api')->user();
        $subscription = $user->subscriptions()->latest()->first();

        if (! $subscription) {
            return response()->json([
                'status'  => false,
                'message' => 'No subscription found.',
                'data'    => [],
            ], 404);
        }

        $plan = $subscription->mealPlan;
        if (! $plan) {
            return response()->json([
                'status'  => false,
                'message' => 'Meal plan not found for this subscription.',
                'data'    => [],
            ], 404);
        }

        $total       = $plan->people * $plan->recipes_per_week;
        $total_price = $plan->price_per_serving * $total;

        $features = SubscriptionFeature::first();

        $data = [
            'plan_name'     => $plan->name,       // e.g., Family Plan
            'price'         => $total_price ?? 0, // e.g., 100.00
            'currency'      => $plan->currency ?? 'AED',
            'billing_cycle' => $plan->billing_cycle ?? 'weekly', // weekly, monthly etc.
                                                                 // 'servings'      => $plan->servings_per_week,         // e.g., 16
            'meals'         => $plan->recipes_per_week,          // e.g., 4
            'people'        => $plan->people ?? 4,

            'features'      => [
                'main_feature'    => $features->description,
                'include_feature' => $features->include_description,
            ],

            'status'        => $subscription->stripe_status, // active / paused / cancelled

        ];

        return response()->json([
            'status'  => true,
            'message' => 'Subscription details retrieved successfully.',
            'data'    => $data,
        ], 200);
    }

}
