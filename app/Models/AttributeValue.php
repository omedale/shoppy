<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $table = 'attribute_value';
    protected $primaryKey = "attribute_value_id";
    public $timestamps = false;
    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute', 'attribute_id');
    }

}
