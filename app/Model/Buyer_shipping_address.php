<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Buyer_shipping_address extends Model
{
    //
    protected $table = 'buyer_shipping_addresses';
    protected $fillable = ['id', 'address', 'city', 'postcode', 'is_active'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
