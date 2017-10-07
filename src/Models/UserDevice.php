<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevice extends Model
{
    use SoftDeletes;
    protected $fillable = ['uuid' , 'device_type' , 'notification_token' , 'app_user_id'];

    /**
     * Set apiToken
     *
     * @return UserDevice
     */
    public function setApiToken()
    {
        $apiToken = uniqid(md5(time()));
        $this->api_token = $apiToken;

        return $this;
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userApp(){
        return $this->belongsTo(UserApp::class, 'app_user_id' , 'id');
    }

    public function userDeviceHistories(){
        return $this->hasMany(UserDeviceHistory::class);
    }
}
