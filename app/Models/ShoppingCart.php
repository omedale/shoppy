<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    protected $table = 'shopping_cart';
    protected $primaryKey = "item_id";
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
