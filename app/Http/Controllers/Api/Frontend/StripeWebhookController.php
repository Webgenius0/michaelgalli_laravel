<?php


namespace App\Http\Controllers\Api\Frontend;

use OpenAI;
use Stripe\Webhook;
use App\Models\User;
use App\Models\Order;
use App\Models\Recipe;
use App\Models\MealPlan;
use Stripe\StripeClient;
use App\Models\Subscription;
use App\Models\WeeklyRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\UserFamilyMember;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response('Webhook signature verification failed.', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // update user stripe id
                $user_id = $session->metadata->user_id ?? null;

                $user = User::find($user_id);
                $user->stripe_id = $session->customer;
                $user->save();


                // If the user is not found, you might want to create a new user or handle it accordingly
                if (!$user) {
                    Log::warning('User not found for email: ' . $session->customer_email);
                    return response('User not found.', 404);
                }

                if ($user) {
                    $stripe = new StripeClient(config('services.stripe.secret'));
                    $subscription = $stripe->subscriptions->retrieve($session->subscription);

                    // log the subscription details
                    Log::info('Stripe Subscription Details: ', [
                        'id' => $subscription->id,
                        'status' => $subscription->status,
                        'price_id' => $subscription->items->data[0]->price->id ?? null,
                        'quantity' => $subscription->items->data[0]->quantity ?? 1,
                        'trial_end' => $subscription->trial_end,
                        'current_period_end' => $subscription->current_period_end,
                    ]);



                    $sub=   Subscription::updateOrCreate(
                        ['stripe_subscription_id' => $subscription->id],
                        [
                            'user_id' => $user->id,
                            'meal_plan_id' => $session->metadata->meal_plan_id ?? null,
                            'type' => 'stripe',
                            'stripe_status' => $subscription->status,
                            'stripe_price' => $subscription->items->data[0]->price->id ?? null,
                            'quantity' => $subscription->items->data[0]->quantity ?? 1,
                            'trial_ends_at' => $subscription->trial_end ? Carbon::createFromTimestamp($subscription->trial_end) : null,
                            'ends_at' => $subscription->cancel_at_period_end ? Carbon::createFromTimestamp($subscription->current_period_end) : null,
                        ]
                    );

                    Log::info('Subscription updated or created for user ID: ' . $sub);
                }
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $subscriptionId = $invoice->subscription;

                $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

                if ($subscription && $subscription->stripe_status === 'active') {
                    $user = $subscription->user;
                    $mealPlan = MealPlan::find($subscription->meal_plan_id);
                    $weekStart = now()->startOfWeek();

                    $totalPrice = $mealPlan->recipes_per_week * $mealPlan->price_per_recipe;


                    if (!$user->orders()->where('week_start', $weekStart)->exists() && $mealPlan) {
                        $recipes = Recipe::inRandomOrder()
                            ->take($mealPlan->recipes_per_week)
                            ->get();

                        Log::info("Creating weekly order for user ID: {$recipes} with meal plan ID: {$mealPlan->id}");

                        $order = $user->orders()->create([
                            'week_start' => $weekStart,
                            'status' => 'completed',
                            'price' => $totalPrice,
                            // 'pric'
                        ]);

                        foreach ($recipes as $recipe) {

                            $order->recipes()->create([
                                'recipe_id' => $recipe->id,
                                'quantity' => 1,
                                'price' => $mealPlan->price_per_recipe ?? 0,
                                'status' => 'completed',
                            ]);



                            // ai generate swap ingredients

                            $ingredients = $recipe->ingredientSections;
                            $members = $user->familyMembers;

                            foreach ($ingredients as $ingredient) {
                                foreach ($members as $member) {
                                    $preferences = $this->getDietaryPreferences($member);
                                    $preferenceString = implode(', ', $preferences);
                                    $swapResult = $this->swapIngredientWithAI($ingredient->title, $preferenceString);
                                 $order_in =      \App\Models\OrderIngredient::create([
                                        'order_id' => $order->id,
                                        'recipe_id' => $recipe->id,
                                        'member_id' => $member->id,
                                        'original_ingredient' => $ingredient->title,
                                        'swapped_ingredient' => $swapResult['swap'],
                                        'reason' => $swapResult['reason'],
                                    ]);
                                }
                            }

                            Log::info($order_in);
                        }
                        Log::info("Weekly order created for user ID: {$user->id}");
                    }
                }
                break;

            default:
                // You can handle more events here if needed
                break;
        }

        return response('Webhook processed', 200);
    }




    public function orderIngredient(Request $request)
    {

        $recipes = Recipe::inRandomOrder()
            ->take(3)
            ->get();

        $user = auth('api')->user();

        foreach ($recipes as $recipe) {


            $ingredients = $recipe->ingredientSections;
            $members = $user->familyMembers;

            foreach ($ingredients as $ingredient) {
                foreach ($members as $member) {
                    $preferences = $this->getDietaryPreferences($member);
                    $preferenceString = implode(', ', $preferences);
                    $swapResult = $this->swapIngredientWithAI($ingredient->title, $preferenceString);

                    \App\Models\OrderIngredient::create([
                        'order_id' => 1,
                        'recipe_id' => $recipe->id,
                        'user_family_member_id' => $member->id,
                        'original_ingredient' => $ingredient->title,
                        'swapped_ingredient' => $swapResult['swap'],
                        'reason' => $swapResult['reason'],
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Ingredients ordered successfully.',
            'data' => $recipes,
        ]);
    }


    protected function getDietaryPreferences(UserFamilyMember $member): array
    {
        $responses = $member->userAnswers()->with('question')->get();

        $preferences = [];

        foreach ($responses as $response) {
            $preferences[$response->question->id] = $response->selected_option_value;
        }

        return $preferences;
    }


    private function swapIngredientWithAI($ingredient, $preference)
    {
        $prompt = "User follows a {$preference} diet. Suggest a suitable swap for the ingredient '{$ingredient}'. Only return the swap and reason.";

        $response = OpenAI::client(config('services.openai.key'))->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a nutrition assistant AI.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $text = trim($response->choices[0]->message->content ?? '');

        // Try parsing
        if (preg_match('/Swap:\s*(.*?)\s*[\r\n]+Reason:\s*(.*)/is', $text, $matches)) {
            return [
                'swap' => trim($matches[1]),
                'reason' => trim($matches[2]),
            ];
        }

        // Fallback if response is not in expected format
        return [
            'swap' => null,
            'reason' => $text,
        ];
    }
}
