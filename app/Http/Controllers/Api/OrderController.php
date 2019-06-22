<?php

namespace App\Http\Controllers\Api;

use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\Tax;
use App\Models\OrderDetail;
use App\Helpers\ErrorHelper;
use App\Helpers\CommonHelper;
use Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function create(Request $request) {
        $get_input = $request->all();
        $rules = [
            'cart_id' => 'required',
            'shipping_id' => 'required',
            'tax_id' => 'required',
        ];

        $messages = [
            'cart_id.required' => 'Cart ID is required',
            'shipping_id.required' => 'Shipping ID is required',
            'tax_id.required' => 'Tax ID is required',
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        if($validator->fails()) {
            return ErrorHelper::USR_02($validator->errors());
        }

        $items = ShoppingCart::where('cart_id', $request->cart_id)->get();

        $order = new Order;
        $order->total_amount = $this->totalAmount($items, $request);
        $order->status = 1;
        $order->tax_id = $request->tax_id;
        $order->shipping_id = $request->shipping_id;
        $order->customer_id = $request->jwt_customer_id;
        $order->created_on = CommonHelper::getCurrentDateTime();
        $order->save();

        $this->saveOrderDetail($items, $order->order_id);
        return response()->json([
            'orderId' => $order->order_id
        ])
        ->setStatusCode(200);
    }

    private function saveOrderDetail($items, $order_id) {
        $order_detail_data = [];
        foreach ($items as $key => $item) {
           array_push($order_detail_data,
                        ['order_id' => $order_id,
                        'product_id' => $item->product_id,
                        'attributes' => $item->attributes,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->product->price]);
        }
        orderDetail::insert($order_detail_data);
    }

    private function totalAmount($items, $request) {
        $items_amounts = $items->map(function ($item) {
            return (float)$item->product->price * $item->quantity;
        });

        $items_total = collect($items_amounts)->sum();
        $tax_percentage = Tax::find($request->tax_id)->value('tax_percentage');
        $shipping_cost = Shipping::find($request->shipping_id)->value('shipping_cost');
        $amount = $items_total + ($items_total * ((float)$tax_percentage/100)) + $shipping_cost;
        $total_amount = number_format(collect($amount)->sum(), 2, '.', '');
        return $total_amount;
    }

}
