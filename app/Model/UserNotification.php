<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = ['user_id', 'heading', 'body', 'type'];

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}
