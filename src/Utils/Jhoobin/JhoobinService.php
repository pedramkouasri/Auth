<?php
/**
 * Created by PhpStorm.
 * User: pedramkousari
 * Date: 9/26/2017
 * Time: 2:59 PM
 */

namespace PedApp\Auth\Utils\Jhoobin;


class JhoobinService
{
    public function subscription($token)
    {
        if(!config('Auth.server')) return true;

        $response = app('jhoobin')->subscriptionStatus($token);

        $decodeResponse = json_decode($response);

        if ($decodeResponse == null) {
            return false;
        }
        if (property_exists($decodeResponse, 'error')) {
            return false;
        }
        if (property_exists($decodeResponse, 'autoRenewing') && $decodeResponse->autoRenewing == null) {
            return false;
        }

        if (property_exists($decodeResponse, 'autoRenewing') && $decodeResponse->autoRenewing != true) {
            return false;
        }
        return true;
    }

}