<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    //
    protected $table = 'attributes';
    //protected $fillable = ['id', 'address', 'city', 'postcode'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
