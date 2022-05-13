<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
	protected $table   = 'cities';
    protected $fillable = ['state_id', 'name'];
    public $timestamps = false;
}
