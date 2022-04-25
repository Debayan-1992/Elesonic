<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use SoftDeletes;
    protected $fillable = ['title','content','image','status','created_by','meta_tags','meta_title','meta_description','meta_keywords',];

    protected $with = ['author'];
    protected $appends = ['avatar'];

    public function author(){
        return $this->belongsTo('App\User', 'created_by');
    }

	public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getAvatarAttribute(){
        if($this->image == null){
            return 'https://wtwp.com/wp-content/uploads/2015/06/placeholder-image.png';
        } else{
            return asset('uploads/blogs/'.$this->image);
        }
    }
}
