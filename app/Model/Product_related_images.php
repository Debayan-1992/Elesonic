<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product_related_images extends Model
{
    //
    protected $table = 'product_related_images';
    //protected $fillable = ['id', 'address', 'city', 'postcode'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
