<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Tax;
use App\Models\ShippingRegion;
use App\Helpers\CommonHelper;

class ConfigController extends Controller
{
    public function filterData() {
        $filter_data = ['departments' => Department::all(),
                        'categories' => Category::all(),
                        'cart_id' => CommonHelper::generateUniqueId(),
                        'attributes' => Attribute::with('attribute_values')->get()];
        return response()->json($filter_data);
    }

    public function checkOutData() {
        $checkout_data = ['tax' => Tax::all(),
                        'shipping_regions' => ShippingRegion::with('shippings')->get()];
        return response()->json($checkout_data);
    }
}
