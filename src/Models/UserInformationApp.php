<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInformationApp extends Model
{
    protected $table = "app_user_informations";
	protected $fillable = [];
    public function userApp(){
        return $this->hasOne(UserApp::class, 'id', 'app_user_id');
    }
}
