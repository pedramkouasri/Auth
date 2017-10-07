<?php
/**
 * Created by PhpStorm.
 * User: mohammadreza
 * Date: 9/26/2017
 * Time: 3:02 PM
 */

namespace PedApp\Auth\Utils\Jhoobin;
use Illuminate\Container;


class Jhoobin
{

    private $accessToken;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken ;
    }

    public function cancel($jhoobinToken)
    {
        return $this->curlExecute($this->urlCreator($jhoobinToken, 'cancel'));
    }

    public function subscriptionMember($jhoobinToken)
    {
        return $this->curlExecute($this->urlCreator($jhoobinToken, 'subscription_member'));
    }

    public function subscriptionStatus($jhoobinToken)
    {
        return $this->curlExecute($this->urlCreator($jhoobinToken, 'subscription_status'));
    }


    private function curlExecute($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 199457ca-8c25-f33a-0c6b-db6448b44930"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    private function urlCreator($jhoobinToken,$method)
    {
        switch ($method){
            case "cancel" :
                $urlPrefix = "https://seller.jhoobin.com/ws/androidpublisher/v2/applications/ir.talktell.map.ganjyab/purchases/subscriptions/cubesku/tokens/";
                $url = $urlPrefix."$jhoobinToken".":cancel?access_token=".$this->accessToken;
                return $url;
                break;
            case "subscription_member" :
                $urlPrefix = "https://seller.jhoobin.com/ws/androidpublisher/v2/applications/ir.talktell.map.ganjyab/purchases/products/cubesku/tokens/";
                $url = $urlPrefix."$jhoobinToken"."?access_token=".$this->accessToken;
                return $url;
                break;
            case "subscription_status" :
                $urlPrefix = "https://seller.jhoobin.com/ws/androidpublisher/v2/applications/ir.talktell.map.ganjyab/purchases/subscriptions/cubesku/tokens/";
                $url = $urlPrefix."$jhoobinToken"."?access_token=".$this->accessToken;
                return $url;
                break;
        }

    }

}