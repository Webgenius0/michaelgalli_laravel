<?php


namespace App\Http\Controllers\Api\Frontend;

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



                    Subscription::updateOrCreate(
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
                                'quantity' => 1, // Assuming quantity is always 1 for simplicity
                                'price' => $mealPlan->price_per_recipe,
                                'status' => 'completed',
                            ]);
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
}
