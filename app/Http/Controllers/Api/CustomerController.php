<?php

namespace App\Http\Controllers\Api;

use Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Customer;
use App\Helpers\ErrorHelper;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        $get_input = $request->all();
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ];

        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email',
            'name.required' => 'Name is required',
            'password.required' => 'Password is required',
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        if($validator->fails()) {
            return ErrorHelper::USR_02($validator->errors());
        }

        $customer =  Customer::where('email', $request->email)->first();
        if(is_null($customer)) {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token = auth()->login($customer);
            return $this->respondWithToken($token, $customer, 201);
        } else {
            return ErrorHelper::USR_04();
        }
    }

    public function login(Request $request)
    {
        $get_input = $request->all();
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email',
            'password.required' => 'Password is required',
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        if($validator->fails()) {
            return ErrorHelper::USR_02($validator->errors());
        }

        $credentials = $request->only(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return ErrorHelper::AUT_02();
        }

        return $this->respondWithToken($token, auth()->user(), 200);
    }

    protected function respondWithToken($token, $customer, $status)
    {
      return response()->json([
        'customer' => [
            'schema' => [
                'customer_id' => $customer->customer_id,
                'name' => $customer->name,
                'email' => $customer->email,
                'address_1' => $customer->address_1,
                'address_2' => $customer->address_2,
                'city' => $customer->city,
                'region' => $customer->region,
                'postal_code' => $customer->postal_code,
                'country' => $customer->country,
                'shipping_region_id' => $customer->shipping_region_id,
                'day_phone' => $customer->day_phone,
                'eve_phone' => $customer->eve_phone,
                'mob_phone' => $customer->mob_phone,
                'credit_card' => $customer->credit_card,
            ]
        ],
        'accessToken' => $token,
        'expires_in' => '24h'
      ])
      ->setStatusCode($status);
    }
}
