<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table = 'categories';
    //protected $fillable = ['id', 'address', 'city', 'postcode', 'is_active'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
