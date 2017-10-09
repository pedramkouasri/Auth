<?php
namespace PedApp\Auth\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserApp;
use App\Models\UserDevice;
use App\Models\UserDeviceHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Http\Response;
use Auth_PedApp;


class ApiAuthController extends Controller
{

    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'uuid' => 'required',
            'deviceType' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator
                    ->errors()
                    ->add('field', 'Something is wrong with this field!')
            , Response::HTTP_BAD_REQUEST);
        }

        // Retrieve by uuid and device_type, or instantiate...
        $userDevice = UserDevice::firstOrCreate([
            'uuid' => $request->get('uuid'),
            'device_type' => $request->get('deviceType'),            
        ] , [
            'notification_token' => $request->get('notificationtoken'),
            'app_user_id' => 0
        ]);

        $data = [
            "message" =>
                'Create: Create User Device or User Device exist but is false'
        ];
//        $this->get('user_device')
//          ->userDeviceHistory($userDevice, UserDeviceHistory::ACTION_CREATE, $data, true);
        event('auth.storeHistory' , [
            $userDevice,
            UserDeviceHistory::ACTION_CREATE,
            $data,
            true
        ]);


        return response()->json(null , Response::HTTP_NO_CONTENT);
    }

    public function verification(Request $request){
        $validator = Validator::make($request->all(), [
            'uuid' => 'required',
            'deviceType' => 'required',
            'phone' => 'required|regex:/^98\d{10}+$/'
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator
                    ->errors()
                    ->add('field', 'Something is wrong with this field!')
                , Response::HTTP_BAD_REQUEST);
        }

        $userDevice = UserDevice::
            where('uuid' , $request->get('uuid'))
            ->where('device_type' , $request->get('deviceType'))
            ->first();

        if (!$userDevice instanceof UserDevice) {
            return response()->json([
                'error' => 'user_device not found'
            ] , Response::HTTP_BAD_REQUEST);
        }

        if ($this->checkVerificationRequest($userDevice) == false) {
            return response()->json([
                'error' => 'checkVerificationRequest'
            ] , Response::HTTP_BAD_REQUEST);
        }


        $phone = $request->get('phone');
        $phone5 = substr($phone, 0, 5);

        $userDevice->is_active = true;


        $jhoobinToken = $request->get('zhobin_token');
        if($jhoobinToken != null){
            $jhoobinTokenCheck = app('jhoobin_service')->subscription(
                $jhoobinToken
            );
            if ($jhoobinTokenCheck == true) {
                $user = UserApp::firstOrCreate(
                    [
                        'phone' => $phone,
                    ]
                );
                
                $userDevice->phone_verification_status = true;
                $userDevice->setApiToken();

                $user->jhoobin_token = $jhoobinToken;
                $user->is_charkhune = true;
                $user->is_active = true;

                $inviteCode = app('helper')->phone2inviteNumber($user->phone);
                $user->invite_code = $inviteCode;
                $user->save();

//                $comment = $post->comments()->save($comment);
                $user->userDevices()->save($userDevice);

//                $this->get('app.sms')->sendSms($user, $text);
                //send sms
                event('auth.auth.onRegisteredSendSms' , [$user]);


                return response()->json(
                    [
                        'apikey' => $userDevice->api_token,
                    ],
                    Response::HTTP_OK
                );
            }
        }

        $user = UserApp::firstOrCreate(
            [
                'phone' => $phone,
            ],[
                'is_active' => true,
            ]
        );

//        $regMessageCount = UserApp::
//            ->where('id' , $user->id)
//            ->whereNotNull('reg_message_id')
//            ->count();


        if (app('helper')->isNotSupportedPhone($phone5) /* && $regMessageCount == 0 */) {
            $notSupportedUser = new NotSupportedPhone();
            $notSupportedUser->phone = $phone;
            $notSupportedUser->data = 'verify';
            $notSupportedUser->save();

            return response(
                'لطفا با شماره ایرانسلی یا همراه اول خود وارد شوید',
                Response::HTTP_NOT_FOUND,
                ['Content-Type'=>'text/plain']
            );

        }

        if (config('Auth.server') != false) {
            $userDevice->verification_code = rand(1000,9999);
        } else {
            $userDevice->verification_code = 1234;
        }

        $user->save();
        $user->userDevices()->save($userDevice);

        if (config('auth.server') != false) {
            if (app('helper')->isHamrahAvalPhone($phone5) == true
                && app('helper')->isIrancellPhone($phone5) == false
               /* && $regMessageCount == 0 */
            ) {
                //send sms
//                app('helper')->sendKavenegarSms(
//                    $user,
//                    $phone,
//                    $userDevice->getVerificationCode(),
//                    'VerifyPool',
//                    $request->getClientIp()
//                );
                //send sms
                event('auth.auth.onRegisteredSendSms' , [$user]);
            } elseif (app('helper')->isIrancellPhone($phone5) == true
                && app('helper') ->isHamrahAvalPhone($phone5) == false
            ) {
                //send sms
//                $this->get('app.sms')->sendSms($user, $userDevice->getVerificationCode());
                event('auth.auth.onRegisteredSendSms' , [$user]);
            }
        }

        $inviteCode = $request->get('invite_code');

        if (!empty($inviteCode)) {
            app('helper')->checkInviteCode($user, $inviteCode);
        }

        return response()->json(null , Response::HTTP_NO_CONTENT);
        
    }

    public function reportVerification(Request $request){
        $validator = Validator::make($request->all(), [
            'uuid' => 'required',
            'deviceType' => 'required',
            'phoneverificationcode' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator
                    ->errors()
                    ->add('field', 'Something is wrong with this field!')
                , Response::HTTP_BAD_REQUEST);
        }

        $userDevice = UserDevice::
            where('uuid' , $request->get('uuid'))
            ->where('device_type' , $request->get('deviceType'))
            ->first();


        if (!$userDevice instanceof UserDevice) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }

        $user = $userDevice->userApp;
        if($userDevice->verification_code != $request->get('phoneverificationcode')){
            $data = ["message" => 'Verification report: User Device verification code error'];
//            $this->get('user_device')
//     ->userDeviceHistory($userDevice, UserDeviceHistory::ACTION_REPORT, $data, false);

            event('auth.storeHistory' , [
                $userDevice,
                UserDeviceHistory::ACTION_REPORT,
                $data,
                false
            ]);

            if ($this->checkVerificationRequest($userDevice) == false) {
                $this->regenerateVerificationCode($user, $userDevice, $request->getClientIp());
            }
            return  response()->json(['error' => 'phone_verification_code not valid'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $userDevice->is_active = false;
            $userDevice->save();
        } catch (\Exception $exception) {

            $data = ["message" => 'Verification report: User Device deactive error'];
//            $this->get('user_device')
//->userDeviceHistory($userDevice, UserDeviceHistory::ACTION_REPORT, $data, false);
            event('auth.storeHistory' , [
                $userDevice,
                UserDeviceHistory::ACTION_REPORT,
                $data,
                false
            ]);

            return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userDevice->phone_verification_status = true;
        $userDevice->is_active = true;
        $token = Auth_PedApp::registerUserDevice($userDevice);
//        $userDevice->setApiToken();
        $userDevice->api_token = $token;

        app('helper')->inviteProcess($user);

        $userDevice->save();

        $data = ["message" => 'Verification report: User Verified Successfully'];

//        $this->get('user_device')
//->userDeviceHistory($userDevice, UserDeviceHistory::ACTION_REPORT, $data, true);
        event('auth.storeHistory' , [
            $userDevice,
            UserDeviceHistory::ACTION_REPORT,
            $data,
            true
        ]);

        return response()->json([
                'apikey' => $token
            ], Response::HTTP_OK);

    }
    /**
     * @param AppUser $user
     * @param UserDevice $userDevice
     * @param $clientIp
     */
    private function regenerateVerificationCode(UserApp $user, UserDevice $userDevice, $clientIp)
    {

        $userDevice->verification_code = rand(1000,9999);
        $userDevice->save();

        $phone = $user->phone;

        $phone5 = substr($phone, 0, 5);

//        $regMessageCount = $this->get('app_user')->registerMessageCount($user);

        if ($this->get('helper')->isHamrahAvalPhone($phone5) == true
            && $this->get('helper')->isIrancellPhone($phone5) == false
           // && $regMessageCount == 0
        ) {
//            $this->get('helper')->sendKavenegarSms(
//                $user,
//                $phone,
//                $userDevice->getVerificationCode(),
//                'VerifyPool',
//                $clientIp
//            );
            //send sms kavenegar
//            event('auth.auth.onRegisteredSendSms' , [$user]);
        } elseif ($this->get('helper')->isIrancellPhone($phone5) == true
            && $this->get('helper') ->isHamrahAvalPhone($phone5) == false
        ) {
            //send sms
//            $this->get('app.sms')->sendSms($user, $userDevice->getVerificationCode());
//            event('auth.auth.onRegisteredSendSms' , [$user]);
        }
    }

    private function checkVerificationRequest(UserDevice $userDevice)
    {
        if (config('Auth.server') == false) {
            return true;
        }

        $now = Carbon::now();
        $yesterday = Carbon::yesterday();

        $userDeviceHistoryCount = UserDeviceHistory::
            where('user_device_id' ,  $userDevice->id)
            ->where('created_at' ,'<',  $now)
            ->where('created_at' ,'>',  $yesterday)
            ->where('action' ,'>',  UserDeviceHistory::ACTION_REPORT)
            ->count();

        if($userDeviceHistoryCount < 4){
            $data = ["message" => 'Sending verification sms'];
//            $this->userDeviceHistory($userDevice, UserDeviceHistory::ACTION_VERIFY, $data, true);
            event('auth.storeHistory' , [
                $userDevice,
                UserDeviceHistory::ACTION_VERIFY,
                $data,
                true
            ]);

            return true;
        }

        $data = ["message" => 'You reached maximum verification request number'];
//        $this->userDeviceHistory($userDevice, UserDeviceHistory::ACTION_VERIFY, $data, false);
        event('auth.storeHistory' , [
            $userDevice,
            UserDeviceHistory::ACTION_VERIFY,
            $data,
            false
        ]);

        return false;
    }

}