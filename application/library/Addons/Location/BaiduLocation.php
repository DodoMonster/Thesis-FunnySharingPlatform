<?php
namespace Addons\Location;
class BaiduLocation {

    private $_ak = 'OiGiOfKnpXpVeYTOooSciYO4'; //service1
    //private $_ak='Ad1GOM1YXUFgfBKGD0agIPxL';//service2
    //private $_ak='cdA5n2AhGVAFQhQDHfus16py';//mobile1
    //private $_ak='UuhvCmRzq075w6Pqvo3zO1jd';//mobile2
    private $_akArray = array('OiGiOfKnpXpVeYTOooSciYO4', 'Ad1GOM1YXUFgfBKGD0agIPxL');

    public function getUserAddress($location) {
        //TODO静态变量问题(自动调用下一个accesskey);
        $data = $this->renderReverse($location, $this->_akArray[0]);
        return $data;
    }

    //逆地址解析
    //ex: http://api.map.baidu.com/geocoder/v2/?ak=E4805d16520de693a3fe707cdc962045&callback=renderReverse&location=39.983424,116.322987&output=json&pois=1
    public function renderReverse($location, $ak) {
        $str=$location['lat'].','.$location['lng'];
        $url = "http://api.map.baidu.com/geocoder/v2/?location=" . $str . "&ak=" . $ak . "&output=json&pois=1";
        $result = \Addons\Grab\Grab::single_grab_json($url);
        if (isset($result['status']) && $result['status'] == 0) {
            $data['province'] = $result['result']['addressComponent']['province'];
            $data['city'] = $result['result']['addressComponent']['city'];
            $data['district'] = $result['result']['addressComponent']['district'];
            $data['street'] = $result['result']['addressComponent']['street'];
            $data['street_number'] = $result['result']['addressComponent']['street_number'];
        } else { 
            $data=array('province'=>'','city'=>'','district'=>'','street'=>'','street_number'=>'');
        }
        return $data;
    }

    //地理编码
    //ex: http://api.map.baidu.com/geocoder/v2/?address=百度大厦&output=json&ak=E4805d16520de693a3fe707cdc962045&callback=showLocation
    public function showLocation($address, $ak) {
        $url = "http://api.map.baidu.com/geocoder/v2/?address=" . $address . "&ak=" . $ak . "&output=json&pois=1";
        $result = \Addons\Grab\Grab::single_grab_json($url);
        return $result;
    }

}
