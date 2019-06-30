<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\INPUT;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Helpers\ErrorHelper;

class ProductController extends Controller
{
    public function index(Request $request, Product $product) {
        $data = $this->buildData($request);
        $description_length = $data['description_length'];

        $products = Product::query();
        if (isset($request->filter)) {
            $filter_data = json_decode($request->filter);
            $products = $this->filterProducts($filter_data, $products, $product);
        }

        return response()->json([
            'count' => $products->count(),
            'rows' => $products
                    ->select(DB::raw("product_id, name, substr(description, 1, $description_length) as description, price, discounted_price, thumbnail, image, image_2"))
                    ->offset($data['offset'])->limit($data['limit'])->get()
        ]);
    }

    public function search(Request $request, Product $product) {
        $get_input = $request->all();
        $search_word = trim(Input::get('q'));
        $data = $this->buildData($request);
        $rules = [
            'q' => 'required',
        ];
        $messages = [
            'q.required' => 'Search query is required'
        ];

        $validator = Validator:: make($get_input, $rules, $messages);
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, ' ', $search_word);
        if($validator->fails() || $term == "") {
            return ErrorHelper::USR_02($validator->errors());
        }

        $products = Product::query();
        if (isset($request->filter)) {
            $filter_data = json_decode($request->filter);
            $products = $this->filterProducts($filter_data, $products, $product);
        }

        $products = $product->searchProduct($products, $data, $this->fullTextSearchTerm($term, $data['all_words']));
        return response()->json([
            'count' => $products->count(),
            'rows' => $products->offset($data['offset'])->limit($data['limit'])->get()
        ]);
    }

    private function filterProducts($filter_data, $products, $product) {
        $has_attribute_filter = isset($filter_data->attribute_value_ids) && count($filter_data->attribute_value_ids) > 0;
        $has_category_filter = isset($filter_data->category_ids) && count($filter_data->category_ids) > 0;
        $has_department_filter = isset($filter_data->department_ids) && count($filter_data->department_ids) > 0;
        $has_price_range = isset($filter_data->price_range) && count($filter_data->price_range) > 0;

        $has_max_min_range = $has_price_range && count($filter_data->price_range) == 2 && $filter_data->price_range[0] > 0 && $filter_data->price_range[1] > 0 && $filter_data->price_range[0] <= $filter_data->price_range[1];
        $has_only_min = !$has_max_min_range && $has_price_range && count($filter_data->price_range) == 2 && $filter_data->price_range[0] > 0 && $filter_data->price_range[1] <= 0;
        $has_only_max = !$has_max_min_range && $has_price_range && count($filter_data->price_range) == 2 && $filter_data->price_range[0] <= 0 && $filter_data->price_range[1] > 0;

        $products = $has_department_filter ? $product->filterByDepartments($products, $filter_data->department_ids) : $products;
        $products = $has_attribute_filter ? $product->filterByAttributes($products, $filter_data->attribute_value_ids) : $products;
        $products = $has_category_filter ? $product->filterByCatgories($products, $filter_data->category_ids) : $products;

        $products = $has_max_min_range ? $product->orWhereBetween('price', $filter_data->price_range) : $products;
        $products = $has_only_min ? $product->orWhere('price', '>=', (float)$filter_data->price_range[0]) : $products;
        $products = $has_only_max ? $product->orWhere('price', '<=', $filter_data->price_range[1]) : $products;
        return $products;
    }

    private function buildData($request) {
        $page = $request->page ? $request->page : 1;
        $data['limit'] = $request->limit ? $request->limit : 20;
        $data['all_words'] = $request->all_words ? $request->all_words : 'on';
        $data['offset'] = ($page - 1) * $data['limit'];
        $data['description_length'] = $request->description_length ? $request->description_length : 200;
        return $data;
    }

    private function fullTextSearchTerm($search_word, $all_words) {
        $search_term = [];

        $search_term = array_map(function ($term) use ($all_words){
            return ($all_words == 'on') ? "+$term*" : "$term*";
        }, array($search_word));
        return $search_term;
    }
}
