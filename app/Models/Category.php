<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    protected $primaryKey = "category_id";
    public $timestamps = false;

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }
}
