<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'banner_title_one',
        'banner_title_two',
        'banner_description',

        'commission',
        'order_amount',
        'charges',

        'fb_link',
        'twitter_link',
        'linkedin_link',
        'instagram_link',

        'name',
        'title',
        'smsflag',
        'smssender',
        'smsuser',
        'smspwd',
        'mailhost',
        'mailport',
        'mailenc',
        'mailuser',
        'mailpwd',
        'mailfrom',
        'mailname',
        'address1',
        'address2',
        'address3',
        'map_embed_link',
        'site_email',
        'site_link',
        'site_number',
        'site_number_office_name',
    ];
    public $timestamps = false;
}
