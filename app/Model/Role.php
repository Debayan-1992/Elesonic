<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    public const IS_SUPERADMIN = 1;
    public const IS_ADMIN = 2; 
    // public const IS_AGENT = 3;  
    // public const IS_BANK = 4; These 2 roles aren't required
    public const IS_CUSTOMER = 5;
    public const IS_SELLER = 6;

    public function getCreatedAtAttribute($value){
        return date('d M y - h:i A', strtotime($value));
    }

    public function getUpdatedAtAttribute($value){
        return date('d M y - h:i A', strtotime($value));
    }
}
