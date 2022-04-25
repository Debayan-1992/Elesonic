<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LoanSlab extends Model
{
    protected $fillable = ['name', 'option1'];
    protected $timestamp = false;
}
