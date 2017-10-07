<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    //for inviter
	public function inviterUser(){
        return $this->belongsTo(UserApp::class , 'inviter_id' , 'id');
    }
    //for invited
	public function invitedUser(){
        return $this->belongsTo(UserApp::class , 'invited_id' , 'id');
    }
}
