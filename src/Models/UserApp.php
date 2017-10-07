<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserApp extends Model
{
    use SoftDeletes;
    protected $table = "app_users";
	protected $fillable = ['phone'];

    /**
     * Set the invite code.
     *
     * @param  string  $value
     * @return void
     */
    public function setIinviteCodeAttribute($inviteCode)
    {
        if(!empty($this->invite_code)){
            $this->attributes['invite_code'] =  $inviteCode;
        }
    }

    public function photoable()
    {
        return $this->morphTo();
    }
    public function userAppInformation(){
        return $this->hasOne(UserInformationApp::class, 'app_user_id', 'id');
    }
    public function userDevice(){
        return $this->hasMany(UserDevice::class, 'app_user_id', 'id');
    }

//    //for inviter
//    public function inviter(){
//        return $this->morphOne(Invite::class , 'inviter_id' , 'id');
//    }
//    //for invited
//    public function invited(){
//        return $this->belongsTo(UserApp::class , 'invited_id' , 'id');
//    }
}
