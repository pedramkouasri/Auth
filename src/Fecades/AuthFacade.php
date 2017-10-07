<?php
/**
 * Created by PhpStorm.
 * User: mohammadreza
 * Date: 9/23/2017
 * Time: 12:26 PM
 */

namespace PedApp\Auth\Fecades;


use Illuminate\Support\Facades\Facade;

class AuthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pedapp-auth';
    }

}