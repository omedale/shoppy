<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $table = 'product';
    protected $primaryKey = "product_id";
    public $timestamps = false;

    public function attributes()
    {
        return $this->belongsToMany('App\Models\AttributeValue', 'product_attribute', 'product_id', 'attribute_value_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'product_category', 'product_id', 'category_id');
    }

    public function filterByAttributes($query, $attribute_value_ids) {
        return $query->orWhereHas('attributes', function($q) use ($attribute_value_ids) {
            $q->whereIn('product_attribute.attribute_value_id', $attribute_value_ids);
        });
    }

    public function filterByCatgories($query, $category_ids) {
        return $query->orWhereHas('categories', function($q) use ($category_ids) {
            $q->whereIn('product_category.category_id', $category_ids);
        });
    }

    public function filterByDepartments($query, $department_ids) {
        return $query->orWhereHas('categories.department', function($q) use ($department_ids) {
            $q->whereIn('department_id', $department_ids);
        });
    }

    public function searchProduct($query, $data, $fullTextSearchTerm) {
        $match = "MATCH(name,description) AGAINST(? IN BOOLEAN MODE)";
        $description_length = $data['description_length'];
        return $query->whereRaw($match, $fullTextSearchTerm)
                    ->select(DB::raw("product_id, name, substr(description, 1,
                    $description_length) as description, price, discounted_price, thumbnail, image, image_2"));
    }
}
