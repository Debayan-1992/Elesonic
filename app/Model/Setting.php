<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['name','title','smsflag','smssender','smsuser','smspwd','mailhost','mailport','mailenc','mailuser','mailpwd','mailfrom','mailname',];
    public $timestamps = false;
}
