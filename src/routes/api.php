<?php

use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->get('api/v1/test', function (){
    $token = JWTAuth::getToken();
    $userDevice =  JWTAuth::toUser($token);
    return $userDevice;
});

Route::group(['prefix' => 'api/v1/auth' , 'namespace' => 'PedApp\Auth\Http\Controllers\V1\Auth'],function (){
    Route::post('/register','ApiAuthController@register');
    Route::post('/verification','ApiAuthController@verification');
    Route::post('/report-verification','ApiAuthController@reportVerification');
});