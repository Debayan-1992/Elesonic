<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'order';
    //protected $fillable = ['id', 'address', 'city', 'postcode'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
