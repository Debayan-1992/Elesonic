<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $table = 'services';
    protected $fillable = ['name', 'slug', 'description', 'image', 'popular', 'status'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
