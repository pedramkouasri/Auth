<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDeviceHistory extends Model
{
    use SoftDeletes;

    const ACTION_CREATE = 'create';
    const ACTION_VERIFY = 'verify';
    const ACTION_REPORT = 'report';


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userDevice(){
        return $this->belongsTo(UserDevice::class);
    }

    public function setDataAttribute($data=null){
        $this->attributes['data'] = $data?json_encode($data):null;
        return $this;
    }
}
