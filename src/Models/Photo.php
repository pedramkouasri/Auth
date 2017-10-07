<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
	protected $fillable = ['name','ext' ,'src'];
	public $timestamps = false;
    public function photoable()
    {
        return $this->morphTo();
    }
    public function getSrcAttribute()
    {
        $str = (strpos($this->attributes['src'] , "http")!== false)?"" : "http://tourist.talktelservice.com/";
        return $str.$this->attributes['src'];
    }
}
