<?php

namespace PedApp\Auth\Events;

use PedApp\Auth\Listener\Register\History;
use PedApp\Auth\Listener\Register\SendSms;

class Register{

    public function subscribe($events){
        $events->listen('auth.onRegisteredSendSms' , SendSms::class.'@onRegistered');
        $events->listen('auth.storeHistory' , History::class.'@store');
    }
}