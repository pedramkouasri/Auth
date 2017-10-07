<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $appends = ["type"];
	protected $fillable = ['title' , 'description' , 'province_id' , 'lat', 'long'];
//	public $timestamps =false;
    public function photos()
    {
        return $this->morphMany(Photo::class,'photoable');
    }

    public function getTypeAttribute()
    {
        return "city";
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function handicrafts()
    {
        return $this->hasMany(Handicraft::class);
    }

    public function localFoods()
    {
        return $this->hasMany(Localfood::class);
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function attractives()
    {
        return $this->hasMany(Attractive::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class,'favoritable');
    }

    public function informations()
    {
        return $this->morphMany(Information::class,'informationable');
    }
}
