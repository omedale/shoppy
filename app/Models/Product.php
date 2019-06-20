<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $primaryKey = "product_id";
    public $timestamps = false;

    public function attributes()
    {
        return $this->belongsToMany('App\Models\AttributeValue', 'product_attribute', 'product_id', 'attribute_value_id');
    }
}
