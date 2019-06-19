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

        if(!Customer::where('email', $request->email)->first()) {
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
            return ErrorHelper::USR_01();
        }

        return $this->respondWithToken($token, auth()->user(), 200);
    }

    public function updateAddress(Request $request)
    {
        $get_input = $request->all();
        $rules = [
            'address_1' => 'required',
            'city' => 'required',
            'region' => 'required',
            'postal_code' => 'required',
            'country' => 'required',
            'shipping_region_id' => 'required',
        ];

        $messages = [
            'address_1.required' => 'Address 1 is required',
            'city.required' => 'City is required',
            'region.required' => 'Region is required',
            'postal_code.required' => 'Postal code is required',
            'country.required' => 'Country is required',
            'shipping_region_id.required' => 'Shipping region id is required',
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        if($validator->fails()) {
            return ErrorHelper::USR_02($validator->errors());
        }

        $customer = Customer::find($request->jwt_customer_id);
        $customer->address_1 = $request->address_1 ? $request->address_1 : $customer->address_1;
        $customer->address_2 = $request->address_2 ? $request->address_2 : $customer->address_2;
        $customer->city = $request->city ? $request->city : $customer->city;
        $customer->region = $request->region ? $request->region : $customer->region;
        $customer->postal_code = $request->postal_code ? $request->postal_code : $customer->postal_code;
        $customer->country = $request->country ? $request->country : $customer->country;
        $customer->shipping_region_id = $request->shipping_region_id ? $request->shipping_region_id : $customer->shipping_region_id;
        $customer->save();
        return response()->json(
            $this->customerSchema($customer)['schema']
            )
            ->setStatusCode(200);

    }

    protected function respondWithToken($token, $customer, $status)
    {
      return response()->json([
        'customer' => $this->customerSchema($customer),
        'accessToken' => $token,
        'expires_in' => '24h'
      ])
      ->setStatusCode($status);
    }

    protected function customerSchema($customer)
    {
        return [
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
        ];
    }
}
