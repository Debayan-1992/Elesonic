<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['banner_title_one','banner_title_two','banner_description','name','title','smsflag','smssender','smsuser','smspwd','mailhost','mailport','mailenc','mailuser','mailpwd','mailfrom','mailname',];
    public $timestamps = false;
}
