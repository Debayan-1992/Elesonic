<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order_details extends Model
{
    //
    protected $table = 'order_details';
    //protected $fillable = ['id', 'address', 'city', 'postcode'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
