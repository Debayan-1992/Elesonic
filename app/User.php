<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'role_id', 'email', 'password', 'mobile', 'profile_image', 'status', 'mobile_verified_at', 'pancard', 'gender', 'dob', 'pincode', 'city_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    protected $appends = ['avatar', 'details'];
    protected $with = ['role'];

    public function role(){
        return $this->belongsTo('App\Model\Role');
    }

    public function getPancardAttribute($value){
        return strtoupper($value);
    }

    public function getDobAttribute($value){
        return date('m/d/Y', strtotime($value));
    }

    public function getCreatedAtAttribute($value){
        return date('d M y', strtotime($value));
    }

    public function getUpdatedAtAttribute($value){
        return date('d M y - h:i A', strtotime($value));
    }

    public function getAvatarAttribute(){
        if($this->profile_image == null){
            return 'https://i.pinimg.com/originals/51/f6/fb/51f6fb256629fc755b8870c801092942.png';
        } else{
            return asset('uploads/profile/'.$this->profile_image);
        }
    }

    public function getDetailsAttribute(){
        if($this->role->slug == 'customer'){
            return Model\CustomerDetail::where('user_id', $this->id)->first();
        }else{
            return Model\CustomerDetail::where('user_id', $this->id)->first();
        }

      
    }
}
