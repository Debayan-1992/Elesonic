<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use SoftDeletes;
    protected $fillable = ['role_id','name','status'];
    protected $with = ['role'];

    public function role(){
        return $this->belongsTo('App\Model\Role');
    }
}
