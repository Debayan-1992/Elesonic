<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LandingBanner extends Model
{
    //
    protected $table = 'landing_banners';
    protected $fillable = ['b_title', 'b_description', 'image', 'status'];

    public function getBannersAttribute()
    {
        return $this;
    }
}
