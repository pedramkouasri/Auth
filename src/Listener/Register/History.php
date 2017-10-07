<?php
/**
 * Created by PhpStorm.
 * User: pedram kousari
 * Date: 9/27/2017
 * Time: 11:51 AM
 */

namespace PedApp\Auth\Listener\Register;

use App\Models\UserDevice;
use App\Models\UserDeviceHistory;

class History
{
    public function store(UserDevice $userDevice , $action , $data , $status){
        $userDeviceHistory = new UserDeviceHistory();
        $userDeviceHistory->action = $action;
        $userDeviceHistory->data = $data;
        $userDeviceHistory->status = $status;

        $userDevice->userDeviceHistories()->save($userDeviceHistory);
    }
}