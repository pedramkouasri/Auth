<?php
/**
 * Created by PhpStorm.
 * User: mohammadreza
 * Date: 9/26/2017
 * Time: 1:56 PM
 */

namespace PedApp\Auth\Utils\Services;


use App\Models\Invite;
use App\Models\UserApp;

class Helper
{
    /**
     * @param $phone5
     * @return bool
     */
    public function isNotSupportedPhone($phone5)
    {
        $supportedArray = config('Auth.supported_phone');

        if (in_array($phone5, $supportedArray)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * @param $phone5
     * @return bool
     */
    public function isIrancellPhone($phone5)
    {
        $irancellArray = config('Auth.irancell_phone');

        if (in_array($phone5, $irancellArray)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $phone5
     * @return bool
     */
    public function isHamrahAvalPhone($phone5)
    {
        $hamrahAvalArray = config('Auth.hamrahaval_phone');

        if (in_array($phone5, $hamrahAvalArray)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $phone
     * @return bool|mixed
     */
    public function filterPhone($phone)
    {
        $numberCheck = preg_match('/\d/', $phone);
        $strLen = strlen($phone);

        if ($strLen < 10) {
            return false;
        }
        if ($strLen > 12) {
            return false;
        }
        if ($numberCheck == 0) {
            return false;
        }
        $phone0 = substr($phone, 0, 1);
        $phone1 = substr($phone, 0, 2);

        if ($phone0 =='9' && $phone1 != '98') {
            $phone = substr_replace($phone, '98', 0, 0);
            return $phone;
        }

        if ($phone0 == '0') {
            $phone = substr_replace($phone, '98', 0, 1);
            return $phone;
        }

        if ($phone1 == '98') {
            return $phone;
        }
    }
    public function phone2inviteNumber($phone)
    {
        $data = $this->getDataForInvite();

        $divider = $data['divider'];
        $codingKey = $data['codingKey'];
        $dataToMap = $data['dataToMap'];

        $part1 = substr($phone,0,5);
        $part2 = substr($phone,5,7);

        $data = $part2*$codingKey;
        $data = substr($data , strlen($data)-7);

        //$data = fmod($part2*$codingKey,$divider);
        $inviteCode = $dataToMap[$part1].$data;

        return $inviteCode;
    }

    public function getDataForInvite(){
        $dataToMap = array(
            "98901"=>"10",
            "98902"=>"11",
            "98903"=>"12",
            "98930"=>"13",
            "98933"=>"14",
            "98935"=>"15",
            "98936"=>"16",
            "98937"=>"17",
            "98938"=>"18",
            "98939"=>"19",
        );

        $mapToData = array(
            "10"=>"98901",
            "11"=>"98902",
            "12"=>"98903",
            "13"=>"98930",
            "14"=>"98933",
            "15"=>"98935",
            "16"=>"98936",
            "17"=>"98937",
            "18"=>"98938",
            "19"=>"98939",
        );

        return [
            'divider' => 10000000,
            'codingKey' => 2339,
            'deCodingKey' => 7858059,
            'dataToMap' => $dataToMap,
            'mapToData' => $mapToData,
        ];
    }

    /**
     * @param AppUser $user
     * @param $inviteCode
     */
    public function checkInviteCode(UserApp $user, $inviteCode)
    {
        $inviteCodeUsed = $user->is_invite_code_used;

        if ($inviteCodeUsed != true) {
            $inviter = UserApp::
                where('invite_code' , $inviteCode)
                ->first();

            if ($inviter instanceof UserApp) {
                if($inviter == $user){
                    return;
                }
                
                //check for exsist inviter_id and invited_id
                $check_exist = Invite::
                    where('inviter_id' , $inviter->id)
                    ->where('invited_id' , $user->id)
                    ->count();

                if($check_exist){
                    return;
                }

                $user->is_invite_code_used = true;
                $isActive = true;
                $this->inviteHistory($user, $inviter, $isActive);
            }
        }
    }
    /**
     * @param AppUser $user
     * @param AppUser $inviter
     * @param $isActive
     */
    public function inviteHistory(UserApp $user, UserApp $inviter, $isActive)
    {
        $invite = new Invite();
        $invite->inviter_id = $inviter->id;
        $invite->invited_id = $user->id;
        $invite->is_active  = $isActive;
        $invite->save();

    }

    public function inviteProcess(UserApp $user)
    {
        $type = Invite::TYPE_OTHER;
        $invite = Invite::
            where('invited_id' , $user->id)
            ->where('type' , $type)
            ->whereIsActive(false)
            ->first();

        if ($invite instanceof Invite) {
            $invite->is_active = true;
            $invite->save();
        }
    }
}