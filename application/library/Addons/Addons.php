<?php
namespace Addons;
class Addons {

    //授权登录
    public static function Login($type, $access_token) {
        $loginObject = null;
        switch ($type) {
            case 'sina':
                $loginObject = new \Addons\Login\Sina($access_token);
                break;
        }

        return $loginObject;
    }

    //推送服务
    public static function Push($sys,$from_user,$device_token,$type,$content) {
        $pushObject = null;
        switch ($sys) {
            case 'ios':
                $pushObject = new \Addons\Push\IosPush();
                $pushObject ->pushMessage($from_user, $device_token, $type, $content);
                break;
            case 'android':
                $pushObject = new \Addons\Push\AndroidPush();
                $pushObject ->pushMessage($from_user, $device_token, $type, $content);
                break;
        }
        return;
    }
    
    //定位服务($param['lat'],$param['lng'],$param['distance'])
    public static function Location($type,$function,$param) {
        $locationObject = null;
        switch ($type) {
            case 'baidu':
                $locationObject = new \Addons\Location\BaiduLocation();
                $data=$locationObject->$function($param);
                break;
            case 'google':
                //include_once SITE_PATH . '/library/Addons/Location/google.class.php';
                //$locationObject = new $type();
                break;
            case 'nearby':
                $locationObject = new \Addons\Location\NearByLocation();
                $data=$locationObject->$function($param);
                break;
        }
        return $data;
    }

}
