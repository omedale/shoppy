<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRegion extends Model
{
    protected $table = 'shipping_region';
    protected $primaryKey = "shipping_region_id";
    public $timestamps = false;

    public function shippings() {
        return $this->hasMany('App\Models\Shipping', 'shipping_region_id');
    }
}
