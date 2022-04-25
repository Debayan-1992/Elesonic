<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = ['role_id', 'scheme_id', 'type_id', 'slab', 'type', 'value'];
    protected $with = ['role', 'scheme', 'loanslab'];
    public $timestamps = false;

    public function role(){
        return $this->belongsTo('App\Model\Role');
    }

    public function scheme(){
        return $this->belongsTo('App\Model\Scheme');
    }

    public function loanslab(){
        return $this->belongsTo('App\Model\LoanSlab', 'slab', 'id');
    }
}
