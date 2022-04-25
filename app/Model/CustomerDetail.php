<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    protected $fillable = ['user_id', 'pancardimage', 'aadharcardimage', 'cancelledchequeimage'];
    public $timestamps = false;
    public $primaryKey = 'user_id';

    protected $appends = ['pancardimagepath', 'aadharcardimagepath', 'cancelledchequeimagepath'];

    public function getPancardimagepathAttribute(){
        if($this->pancardimage == null){
            return 'https://m.jagranjosh.com/imported/images/E/Articles/PAN-Card-Structure-benefits.jpg';
        } else{
            return asset('uploads/profile/customers/'.$this->pancardimage);
        }
    }

    public function getAadharcardimagepathAttribute(){
        if($this->aadharcardimage == null){
            return 'https://i.pinimg.com/originals/83/b3/0f/83b30f1a065cbf872c0c945602b14503.jpg';
        } else{
            return asset('uploads/profile/customers/'.$this->aadharcardimage);
        }
    }

    public function getCancelledchequeimagepathAttribute(){
        if($this->cancelledchequeimage == null){
            return 'https://www.harishgade.com/wp-content/uploads/2019/12/cancelled-cheque.jpg';
        } else{
            return asset('uploads/profile/customers/'.$this->cancelledchequeimage);
        }
    }
}
