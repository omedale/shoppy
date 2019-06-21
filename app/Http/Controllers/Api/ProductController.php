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
    public function index(Request $request) {
        $data = $this->buildData($request);
        $description_length = $data['description_length'];
        return response()->json([
            'count' => Product::count(),
            'rows' => Product::with('attributes')
                    ->select(DB::raw("product_id, name, substr(description, 1, $description_length) as description, price, discounted_price, thumbnail, image, image_2"))
                    ->offset($data['offset'])->limit($data['limit'])->get()
        ]);
    }

    public function search(Request $request) {
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

        $match = "MATCH(name,description) AGAINST(? IN BOOLEAN MODE)";
        $description_length = $data['description_length'];
        $products = Product::query()->with('attributes')
                    ->whereRaw($match, $this->fullTextSearchTerm($term, $data['all_words']))
                    ->select(DB::raw("product_id, name, substr(description, 1, $description_length) as description, price, discounted_price, thumbnail, image, image_2"));

        return response()->json([
            'count' => $products->count(),
            'rows' => $products->offset($data['offset'])->limit($data['limit'])->get()
        ]);
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