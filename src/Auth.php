<?php
namespace PedApp\Auth;

use App\Models\UserApp;
use App\Models\UserDevice;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Auth
{
    public function authenticate(){
        //get user data
        $credentials = request()->only('email' , 'password');
        try{
            $token = JWTAuth::attempt($credentials);
            if(!$token){
                return response()->json(['error' => 'invalid_credentials'] , Response::HTTP_UNAUTHORIZED);
            }

        }catch (JWTException $e){
            return response()->json(['error' => 'something_went_wrong'] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(['token' => $token] , Response::HTTP_OK);
    }

    public function register(){
        $email = request()->email;
        $name = request()->name;
        $password = request()->password;

        $user = UserApp::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(['token' => $token] , Response::HTTP_OK);

    }

    public function registerUserDevice(UserDevice $userDevice){
        $token = JWTAuth::fromUser($userDevice);
        return $token;
    }

    public function authenticateWithPhone($phone){
        //get user data
        $credentials = ['phone' => $phone];
        try{
            $token = JWTAuth::attempt($credentials);
            if(!$token){
                return response()->json(['error' => 'invalid_credentials'] , Response::HTTP_UNAUTHORIZED);
            }

        }catch (JWTException $e){
            return response()->json(['error' => 'something_went_wrong'] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(['token' => $token] , Response::HTTP_OK);
    }

}