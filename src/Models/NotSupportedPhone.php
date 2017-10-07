<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotSupportedPhone extends Model
{
	protected $fillable = ['phone'];
    public function photoable()
    {
        return $this->morphTo();
    }
}
