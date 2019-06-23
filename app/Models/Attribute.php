<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attribute';
    protected $primaryKey = "attribute_id";
    public $timestamps = false;

    public function attribute_values() {
        return $this->hasMany('App\Models\AttributeValue', 'attribute_id');
    }
}
