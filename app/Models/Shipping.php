<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $table = 'shipping';
    protected $primaryKey = "shipping_id";
    public $timestamps = false;
}
