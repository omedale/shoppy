<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Helpers\ErrorHelper;

class ProductController extends Controller
{
    public function index(Request $request) {
        $page = $request->page ? $request->page : 1;
        $limit = $request->limit ? $request->limit : 20;
        $offset = ($page - 1) * $limit;
        $description_length = 200;
        return response()->json([
            'count' => Product::count(),
            'rows' => Product::with('attributes')
                    ->select(DB::raw("product_id, name, substr(description, 1, $description_length) as description, price, discounted_price, thumbnail, image, image_2"))
                    ->offset($offset)->limit($limit)->get()
        ]);
    }
}
