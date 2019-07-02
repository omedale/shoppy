<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Customer;
use App\Models\ShoppingCart;
use App\Helpers\ErrorHelper;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Input;

class ShoppingCartController extends Controller
{
    public function add(Request $request) {
        $get_input = $request->all();
        $rules = [
            'cart_id' => 'required',
            'product_id' => 'required',
            'attributes' => 'required',
        ];

        $messages = [
            'cart_id.required' => 'Cart ID is required',
            'product_id.required' => 'Product ID is required',
            'attributes.required' => 'Attributes is required',
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        if($validator->fails()) {
            return ErrorHelper::USR_02($validator->errors());
        }

        if (!$product = Product::find($request->product_id)) {
            return ErrorHelper::NOT_FOUND('Product');
        };

        $attributes = INPUT::get('attributes');
        $item = new ShoppingCart;
        $item->product_id = $request->product_id;
        $item->cart_id = $request->cart_id;
        $item->attributes = $attributes;
        $item->quantity = 1;
        $item->buy_now = true;
        $item->added_on = CommonHelper::getCurrentDateTime();
        $item->save();

        $cart_items = $this->getAllItems($request->cart_id);
        return response()->json($cart_items)
          ->setStatusCode(200);
    }

    public function update(Request $request, $item_id) {
        $get_input = INPUT::all();
        $rules = [
            'quantity' => 'required',
        ];

        $messages = [
            'quantity.required' => 'Quantity is required'
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        if($validator->fails() || !isset($request->item_id)) {
            return ErrorHelper::USR_02($validator->errors());
        }

        if (!$item = ShoppingCart::find($request->item_id)) {
            return ErrorHelper::NOT_FOUND('Cart');
        };
        $item->quantity = $request->quantity;
        $item->save();

        $cart_items = $this->getAllItems($item->cart_id);

        return response()->json($cart_items)
          ->setStatusCode(200);
    }

    public function getCartItems(Request $request) {
        if(!isset($request->cart_id)) {
            return ErrorHelper::USR_02($validator->errors());
        }

        $cart_items = $this->getAllItems($request->cart_id);
        return response()->json(
            $cart_items
        )
        ->setStatusCode(200);
    }

    public function generateUniqueId() {
        return response()->json(
            [
                'cart_id' => CommonHelper::generateUniqueId()
            ]
            );
    }

    private function getAllItems($cart_id) {
        return ShoppingCart::where('cart_id', $cart_id)->get()->map(function($item) {
            return $this->cartItem($item);
        });
    }

    public function removeProduct(Request $request, $item_id) {
        if ($item = ShoppingCart::find($item_id)) {
            $item->delete();
        }
        return response()->json()
          ->setStatusCode(200);
    }

    private function cartItem($item) {
        $price = (float)$item->product->discounted_price > 0 ? $item->product->discounted_price : $item->product->price;
        return [
            "item_id" => $item->item_id,
            "name" => $item->product->name,
            "attributes" => $item->attributes,
            "product_id" => $item->product_id,
            "price" => $price,
            "discounted_price" => $item->product->discounted_price,
            "quantity" => (int)$item->quantity,
            "image" => $item->product->image,
            "thumbnail" => $item->product->thumbnail,
            "subtotal" => ((float)$price * $item->quantity),
        ];
    }
}
