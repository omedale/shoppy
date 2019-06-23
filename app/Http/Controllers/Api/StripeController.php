<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Helpers\ErrorHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe;

class StripeController extends Controller
{
    public function charge(Request $request) {

        $get_input = $request->all();
        $rules = [
            'stripeToken' => 'required',
            'order_id' => 'required',
            'description' => 'required',
            'amount' => 'required'
        ];

        $messages = [
            'stripeToken.required' => 'stripeToken is required',
            'order_id.required' => 'Order ID is required',
            'amount.required' => 'Amount is required',
            'description.required' => 'Description is required',
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        if($validator->fails()) {
            return ErrorHelper::USR_02($validator->errors());
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));
        $charge = \Stripe\Charge::create ([
                "amount" => 100 * $request->amount,
                "currency" => $request->currency ? $request->currency : "usd",
                "source" => $request->stripeToken,
                "description" => "Turing items payment"
        ]);

        return response()->json($charge)
        ->setStatusCode(200);
    }
}
