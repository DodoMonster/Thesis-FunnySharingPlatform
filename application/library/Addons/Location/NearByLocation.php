<?php
namespace Addons\Location;
class NearByLocation {
    public $earthRadius=6371.004;
    //获得用户的范围
    public function getUserSquarePoint($param){
        $lat=$param['lat'];
        $lng=$param['lng'];
        $distance =!empty($param['distance'])?$param['distance']: 0.5;
        $dlng = rad2deg(2 * asin(sin($distance / (2 * $this->earthRadius)) / cos(deg2rad($lat))));
        $dlat = rad2deg($distance/$this->earthRadius);
        return array(
            'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
            'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
            'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
            'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );
    }
    
    /**
    *  @desc 根据两点间的经纬度计算距离
    *  @param float $lat 纬度值
    *  @param float $lng 经度值
    */
     public function getDistance($param){
         //Convert these degrees to radians to work with the formula
         $lat1= deg2rad($param['lat1']);
         $lng1= deg2rad($param['lng1']);
         $lat2= deg2rad($param['lat2']);
         $lng2= deg2rad($param['lng2']);
        //Using the Haversine formula calculate the distance
        //http://en.wikipedia.org/wiki/Haversine_formula
         $calcLongitude = $lng2 - $lng1;
         $calcLatitude = $lat2 - $lat1;
         $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  
         $calculatedDistance = 2 * $this->earthRadius * 1000 * asin(min(1, sqrt($stepOne)));//千米转换为米
         return round($calculatedDistance);
     }
}

