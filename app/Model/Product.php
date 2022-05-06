<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'products';
    //protected $fillable = ['id', 'address', 'city', 'postcode'];
    protected $appends = []; //getting data from other models
    public function category()
    {
        return $this->belongsTo(Category::class)->select('id','name','slug');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
