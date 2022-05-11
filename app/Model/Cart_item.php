<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Cart_item extends Model
{
    protected $table = 'cart_item';
   
    public function product()
    {
        return $this->hasMany(Product::class, 'id','cart_item_id')->select('id','name','slug','quantity','photos','unit_price','purchase_price','discount');
    }
}
