<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Service_booking extends Model
{
    //
    protected $table = 'service_booking';
    protected $fillable = [
    'name',
    'email',
    'service_acceptance_status', 
    'message',
    'service_offered_price',
    'service_request_acceptance_date',
];

}
