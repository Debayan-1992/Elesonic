<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = ['role_id','name','slug','original_price','offered_price','description','validity','status','featured'];
    protected $with = ['role'];
    protected $appends = ['purchase_price'];

    public function role(){
        return $this->belongsTo('App\Model\Role');
    }

    public function getPurchasePriceAttribute(){
        $value = $this->original_price;

        if($this->offered_price > 0){
            $value = $this->offered_price;
        }

        return $value;
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
