<?php

namespace App\Http\Controllers\Api\Gateway\Stripe;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StripeCallBackController extends Controller
{
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(false, 'Validation failed', 422, $validator->errors());
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            
            $data = $validator->validated();
            $uid = Str::uuid(); 

            $redirectUrl = route('api.payment.stripe.success') . '?token={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('api.payment.stripe.cancel');

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'donation'
                        ],
                        'unit_amount' => $data['price'] * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => [
                    'order_id' => $uid,
                    'user_id' => auth('api')->user()->id
                ],
                'success_url' => $redirectUrl,
                'cancel_url' => $cancelUrl,
            ]);

            return Helper::jsonResponse(true, 'Checkout session created successfully', 200, $session->url); 

        } catch (ModelNotFoundException $e) {

            return redirect()->to(env("APP_URL")."/fail");

        } catch (ApiErrorException $e) {

            return redirect()->to(env("APP_URL")."/fail");

        }
    }

    public function success(Request $request)
    { 
        $validatedData = $request->validate([
            'token' => ['required', 'string']
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = Session::retrieve($validatedData['token']);
            if ($session->payment_status === 'paid') {

                Transaction::create([
                    'user_id'   => $session->metadata['user_id'],
                    'amount'    => $session->amount_total / 100,
                    'currency'  => $session->currency,
                    'trx_id'    => $session->id,
                    'type'      => 'increment',
                    'status'    => 'success',
                    'metadata'  => json_encode($session->metadata)
                ]);

                return redirect()->to(env("APP_URL")."/success");
            }

            if ($session->payment_status === 'unpaid' || $session->payment_status === 'no_payment_required') {
                return redirect()->to(env("APP_URL")."/fail");
            }

            return redirect()->to(env("APP_URL")."/fail");

        } catch (ApiErrorException $e) {

            return redirect()->to(env("APP_URL")."/fail");

        } catch (ModelNotFoundException $e) {

            return redirect()->to(env("APP_URL")."/fail");

        }
    }


    public function failure(Request $request)
    {
        return redirect()->to(env("APP_URL")."/fail");
    }
        

}
