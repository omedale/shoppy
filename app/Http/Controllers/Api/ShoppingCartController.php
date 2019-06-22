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
use Illuminate\Support\Facades\INPUT;

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

        return response()->json([
            $this->cartResponse($item, $product)
          ])
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
        if($validator->fails()) {
            return ErrorHelper::USR_02($validator->errors());
        }

        if (!$item = ShoppingCart::find($request->item_id)) {
            return ErrorHelper::NOT_FOUND('Cart');
        };
        $item->quantity = $request->quantity;
        $item->save();

        $product = Product::find($item->product_id);
        return response()->json([
            $this->cartResponse($item, $product)
          ])
          ->setStatusCode(200);
    }

    public function removeProduct(Request $request, $item_id) {
        if ($item = ShoppingCart::find($item_id)) {
            $item->delete();
        }
        return response()->json()
          ->setStatusCode(200);
    }

    private function cartResponse($item, $product) {
        return [
            "item_id" => $item->item_id,
            "name" => $product->name,
            "attributes" => $item->attributes,
            "product_id" => $item->product_id,
            "price" => $product->price,
            "quantity" => (int)$item->quantity,
            "image" => $product->image,
            "subtotal" => ($product->price * $item->quantity),
        ];
    }
}
