<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\Subscription;
use App\Models\SubscriptionFamilyMember;
use App\Models\User;
use App\Models\UserFamilyMember;
use App\Models\UserPlanCart;
use App\Models\UserRecipeCart;
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

                    $cart = UserPlanCart::where('user_id', $user->id)->first();

                    $sub = Subscription::updateOrCreate(
                        ['stripe_subscription_id' => $subscription->id,
                            'user_id'                 => $user->id,
                        ],
                        [
                            'meal_plan_id'  => $cart->meal_plan_id ?? null,
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

                        $recipe_ids = UserRecipeCart::where('user_id', $user->id)->pluck('recipe_id')->toArray();

                        $recipes = Recipe::whereIn('id', $recipe_ids)->get();

                        $user_plan_cart = UserPlanCart::where('user_id', $user->id)->first();

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
                                'price'     => $user_plan_cart->price_per_serving ?? 0,
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

                    // $user_recipe_cart = UserRecipeCart::where('user_id', $user->id)->first();
                    // $user_recipe_cart->delete();
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
        $prompt = "User follows a {$preference} diet. Suggest a suitable swap for the ingredient '{$ingredient}'. Only return the swap and reason. The reason must be no more than 10 words.";

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
            $reason = trim($matches[2]);

            // Trim reason to 10 words max, if needed
            $words = preg_split('/\s+/', $reason);
            if (count($words) > 10) {
                $reason = implode(' ', array_slice($words, 0, 10));
            }

            return [
                'swap'   => trim($matches[1]),
                'reason' => $reason,
            ];
        }

        // response is not in expected format
        return [
            'swap'   => null,
            'reason' => $text,
        ];
    }

}
