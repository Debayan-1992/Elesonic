<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Buyer_billing_address extends Model
{
    //
    protected $table = 'buyer_billing_addresses';
    protected $fillable = ['id', 'address', 'city', 'postcode'];
    protected $with = []; //For eager loading
    protected $appends = []; //getting data from other models
}
