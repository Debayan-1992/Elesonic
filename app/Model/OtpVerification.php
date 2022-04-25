<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = ['email', 'mobile', 'otp'];

    public function setOtpAttribute($value)
    {
        // $this->attributes['otp'] = bcryt($value);
        $this->attributes['otp'] = bcrypt('111111');
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}
