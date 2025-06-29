<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\Subscription;
use App\Models\SubscriptionFamilyMember;
use App\Models\User;
use App\Models\UserFamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use OpenAI;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $payload         = @file_get_contents('php://input');
        $sig_header      = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
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
                $user_id = $session->metadata->user_id ?? null;

                $memberIdsString = $session->metadata->member_ids ?? '';
                $memberIds       = array_filter(explode(',', $memberIdsString));

                $user            = User::find($user_id);
                $user->stripe_id = $session->customer;
                $user->save();

                if (! $user) {
                    Log::warning('User not found for email: ' . $session->customer_email);
                    return response('User not found.', 404);
                }

                if ($user) {
                    $stripe       = new StripeClient(config('services.stripe.secret'));
                    $subscription = $stripe->subscriptions->retrieve($session->subscription);

                    $sub = Subscription::updateOrCreate(
                        ['stripe_subscription_id' => $subscription->id],
                        [
                            'user_id'       => $user->id,
                            'meal_plan_id'  => $session->metadata->meal_plan_id ?? null,
                            'type'          => 'stripe',
                            'stripe_status' => $subscription->status,
                            'stripe_price'  => $subscription->items->data[0]->price->id ?? null,
                            'quantity'      => $subscription->items->data[0]->quantity ?? 1,
                            'trial_ends_at' => $subscription->trial_end ? Carbon::createFromTimestamp($subscription->trial_end) : null,
                            'ends_at'       => $subscription->cancel_at_period_end ? Carbon::createFromTimestamp($subscription->current_period_end) : null,
                        ]
                    );

                    // user family member subscribe
                    foreach ($memberIds as $memberId) {
                        $user_subscription = SubscriptionFamilyMember::create([
                            'subscription_id'       => $sub->id,
                            'user_family_member_id' => $memberId,
                        ]);
                    }

                }
                break;

            case 'invoice.payment_succeeded':

                $invoice        = $event->data->object;
                $subscriptionId = $invoice->subscription;

                $subscription = Subscription::with('familyMembers')->where('stripe_subscription_id', $subscriptionId)->first();

                if ($subscription && $subscription->stripe_status === 'active') {

                    $user     = $subscription->user;
                    $mealPlan = MealPlan::find($subscription->meal_plan_id);

                    $weekStart  = now()->startOfWeek();
                    $totalPrice = $mealPlan->recipes_per_week * $mealPlan->price_per_recipe;

                    if (! $user->orders()->where('week_start', $weekStart)->exists() && $mealPlan) {

                        // যদি customer checkout করার সময় ৩টি recipe নির্বাচন করে এবং Stripe Checkout Session এর metadata তে recipe IDs পাঠানো হয় (যেমন: recipe_ids = 5,9,13), তাহলে আপনি এই metadata থেকে ওই IDs read করে সেই অনুযায়ী $recipes পাবেন।

                        $recipeIdsString = $invoice->metadata->recipe_ids ?? '';
                        $recipeIds       = array_filter(explode(',', $recipeIdsString));

                        // if (! empty($recipeIds)) {
                        $recipes = Recipe::whereIn('id', $recipeIds)->get();
                        Log::info("Recipe List: ", $recipes);
                        // } else {
                        //     $recipes = Recipe::inRandomOrder()
                        //         ->take($mealPlan->recipes_per_week)
                        //         ->get();
                        // }

                        // $recipes = Recipe::inRandomOrder()
                        //     ->take(3)
                        //     ->get();

                        $order = $user->orders()->create([
                            'week_start' => $weekStart,
                            'status'     => 'completed',
                            'price'      => $totalPrice,
                            // 'pric'
                        ]);

                        foreach ($recipes as $recipe) {

                            $order->recipes()->create([
                                'recipe_id' => $recipe->id,
                                'quantity'  => 1,
                                'price'     => $mealPlan->price_per_recipe ?? 0,
                                'status'    => 'completed',
                            ]);

                            // ai generate swap ingredients

                            $ingredients = $recipe->ingredientSections;
                            $members     = $subscription->familyMembers;

                            foreach ($ingredients as $ingredient) {
                                foreach ($members as $member) {
                                    $preferences      = $this->getDietaryPreferences($member);
                                    $preferenceString = implode(', ', $preferences);
                                    $swapResult       = $this->swapIngredientWithAI($ingredient->title, $preferenceString);
                                    $order_ingredient = \App\Models\OrderIngredient::create([
                                        'order_id'              => $order->id,
                                        'recipe_id'             => $recipe->id,
                                        'user_family_member_id' => $member->id,
                                        'original_ingredient'   => $ingredient->title,
                                        'swapped_ingredient'    => $swapResult['swap'],
                                        'reason'                => $swapResult['reason'],
                                    ]);
                                }
                            }
                        }
                    }
                }
                break;

            default:

                break;
        }

        return response('Webhook processed', 200);
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
            'model'    => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a nutrition assistant AI.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $text = trim($response->choices[0]->message->content ?? '');

        // Try parsing
        if (preg_match('/Swap:\s*(.*?)\s*[\r\n]+Reason:\s*(.*)/is', $text, $matches)) {
            return [
                'swap'   => trim($matches[1]),
                'reason' => trim($matches[2]),
            ];
        }

        // response is not in expected format
        return [
            'swap'   => null,
            'reason' => $text,
        ];
    }
}
