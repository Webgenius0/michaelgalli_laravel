<?php

namespace App\Http\Controllers\Api\Frontend;

use Stripe\Stripe;
use App\Models\MealPlan;
use Stripe\StripeClient;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    // meal plan
    public function mealPlans()
    {
        $mealPlans = MealPlan::all();

        if ($mealPlans->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No meal plans available.',
                'data' => [],
            ], 404);
        }


        return response()->json([
            'status' => true,
            'message' => 'Meal plans retrieved successfully.',
            'data' => $mealPlans,
        ], 200);
    }


    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meal_plan_id' => 'required|exists:meal_plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        $meal_plan = MealPlan::find($request->meal_plan_id);

        // dd($meal_plan);

        if (!$meal_plan || !$meal_plan->stripe_price_id) {
            return response()->json([
                'status' => false,
                'message' => 'Meal plan not found or does not have a Stripe price ID.',
                'data' => [],
            ], 404);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $checkoutSession = Session::create([
                'line_items' => [[
                    'price' => $meal_plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('subscription.success', ['user_id' => auth('api')->id()]),
                'cancel_url' => route('subscription.cancel', ['user_id' => auth('api')->id()]),
                'metadata' => [
                    'user_id' => auth('api')->id(),
                    'meal_plan_id' => $meal_plan->id,
                ],
            ]);

            // dd($checkoutSession);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error creating checkout session: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        } catch (IncompletePayment $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment incomplete: ' . $e->getMessage(),
                'data' => [],
            ], 400);
        }

        $checkout_url = $checkoutSession->url;

        return response()->json([
            'status' => true,
            'message' => 'Checkout session created successfully.',
            'data' => [
                'checkout_url' => $checkout_url,
            ],
        ], 200);
    }




    public function success($user_id)
    {
        // Handle successful subscription logic here
        return response()->json([
            'status' => true,
            'message' => 'Subscription successful for user ID: ' . $user_id,
            'data' => [],
        ], 200);
    }

    public function cancel($user_id)
    {
        // Handle subscription cancellation logic here
        return response()->json([
            'status' => false,
            'message' => 'Subscription cancelled for user ID: ' . $user_id,
            'data' => [],
        ], 200);
    }

    // pause subscription
    public function pauseSubscription(Request $request)
    {
        $user = auth('api')->user();
        $subscription = $user->subscriptions()->where('stripe_status', 'active')->first();

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'No active subscription found.',
                'data' => [],
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
            'status' => true,
            'message' => 'Subscription paused successfully.',
            'data' => $stripeSub,
        ], 200);
    }

    // cancel subscription
    public function cancelSubscription(Request $request)
    {
        $user = auth('api')->user();
        $subscription = $user->subscriptions()->where('stripe_status', 'active')->first();

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'No active subscription found.',
                'data' => [],
            ], 404);
        }

        // Stripe API call to cancel subscription
        $stripeSub = $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);

        // Update local DB
        $subscription->stripe_status = 'canceled';
        $subscription->save();

        return response()->json([
            'status' => true,
            'message' => 'Subscription cancelled successfully.',
            'data' => $stripeSub,
        ], 200);


        
    }



    
}
