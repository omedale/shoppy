<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $primaryKey = "department_id";
    protected $hidden = ['description'];
    public $timestamps = false;
}
