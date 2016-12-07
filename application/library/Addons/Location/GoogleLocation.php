<?php

class google {

    //private $_serverkey='AIzaSyAtYUTUpO6Ze05DQG0N077tjt9dr6OQLj8';
    private $_serverkey = 'AIzaSyCAC8eU4lj56zRHuWjJuDu7FCkatb8fIdM';
    private static $_status = 0;

    //单个文本搜索
    public function textSearch($text) {
        $serverkey = $this->_serverkey;
        $reasultsArray = array();
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($text) . '&sensor=true&key=' . $serverkey . '&language=zh-CN';
        $results = Lib::single_grab_json_use_proxy($url);
        $reasultsArray = $results['results'];
        return $reasultsArray;
    }

    //多个文本搜索
    public function textSearches($texts) {
        $serverkey = $this->_serverkey;
        $resultsArray = array();
        $nodes = array();
        foreach ($texts as $k => $v) {
            $nodes[] = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($v) . '&sensor=true&key=' . $serverkey . '&language=zh-CN';
        }
        $results = Lib::multiple_grab_json_use_proxy($nodes);
        if (!empty($results)) {
            $i = 0;
            foreach ($results as $kt => $vt) {
                $tmpDatas = $results[$kt]['results'];
                foreach ($tmpDatas as $km => $vm) {
                    $tmp = array();
                    $tmp['id'] = $vm['id'];
                    $tmp['name'] = $vm['name'];
                    $tmp['seller_name'] = $texts[$i];
                    $tmp['formatted_address'] = $vm['formatted_address'];
                    $tmp['types'] = isset($vm['types']) ? implode(',', $vm['types']) : '';
                    $tmp['lat'] = $vm['geometry']['location']['lat'];
                    $tmp['lng'] = $vm['geometry']['location']['lng'];
                    $tmp['reference'] = $vm['reference'];
                    $resultsArray[] = $tmp;
                }
                $i++;
            }
        }
        return $resultsArray;
    }

    //单个详细搜索
    public function placeDetailSearch($reference) {
        $serverkey = $this->_serverkey;
        $url = "https://maps.googleapis.com/maps/api/place/details/json?reference=" . $reference . "&sensor=true&key=" . $serverkey . "&language=zh-CN";
        $results = Lib::single_grab_json_use_proxy($url);
        return $results;
    }

    //多个详细搜索
    public function placeDetailSearches($references) {
        $serverkey = $this->_serverkey;
        $resultsArray = array();
        $nodes = array();
        foreach ($references as $k => $v) {
            $nodes[] = 'https://maps.googleapis.com/maps/api/place/details/json?reference=' . urlencode($v) . '&sensor=true&key=' . $serverkey . '&language=zh-CN';
        }
        $results = Lib::multiple_grab_json_use_proxy($nodes);
        if (!empty($results)) {
            foreach ($results as $kt => $vt) {
                if ($results[$kt]['status'] == 'OK') {
                    $tmpResult = $vt['result'];
                    $tmp = array();
                    if (isset($tmpResult['address_components'])) {
                        foreach ($tmpResult['address_components'] as $kn => $vn) {
                            if ($tmpResult['address_components'][$kn]['types'][0] == 'street_number') {
                                $tmp['street_number'] = $vn['long_name'];
                                continue;
                            }
                            if ($tmpResult['address_components'][$kn]['types'][0] == 'route') {
                                $tmp['route'] = $vn['long_name'];
                                continue;
                            }
                            if ($tmpResult['address_components'][$kn]['types'][0] == 'sublocality') {
                                $tmp['sublocality'] = $vn['long_name'];
                                continue;
                            }
                            if ($tmpResult['address_components'][$kn]['types'][0] == 'locality') {
                                $tmp['locality'] = $vn['long_name'];
                                continue;
                            }
                            if ($tmpResult['address_components'][$kn]['types'][0] == 'administrative_area_level_1') {
                                $tmp['administrative_area_level_1'] = $vn['long_name'];
                                continue;
                            }
                            if ($tmpResult['address_components'][$kn]['types'][0] == 'country') {
                                $tmp['country'] = $vn['long_name'];
                                continue;
                            }
                        }
                    }
                    $tmp['id'] = $tmpResult['id'];
                    $tmp['formatted_phone_number'] = isset($tmpResult['formatted_phone_number']) ? $tmpResult['formatted_phone_number'] : '';
                    $tmp['international_phone_number'] = isset($tmpResult['international_phone_number']) ? $tmpResult['international_phone_number'] : '';
                    $tmp['website'] = isset($tmpResult['website']) ? $tmpResult['website'] : '';
                    $resultsArray[] = $tmp;
                }
            }
        }
        return $resultsArray;
    }

    //逆地址解析
    public function reverseGeocoding($location) {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" . $location . "&sensor=true";
        $result = Lib::single_grab_json_use_proxy($url);
        return $result;
    }

    //获得用户地址
    public function getUserAddress($location) {
        $result = $this->reverseGeocoding($location);
        $tmp = array();
        if (isset($result['status']) && $result['status'] == 'OK') {
            $tmpResult = $result['results'][0];
            if (isset($tmpResult['address_components'])) {
                foreach ($tmpResult['address_components'] as $kn => $vn) {
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'street_number') {
                        $tmp['street_number'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'route') {
                        $tmp['route'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'neighborhood') {
                        $tmp['neighborhood'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'sublocality') {
                        $tmp['sublocality'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'locality') {
                        $tmp['locality'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'administrative_area_level_3') {
                        $tmp['administrative_area_level_3'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'administrative_area_level_2') {
                        $tmp['administrative_area_level_2'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'administrative_area_level_1') {
                        $tmp['administrative_area_level_1'] = $vn['long_name'];
                        continue;
                    }
                    if ($tmpResult['address_components'][$kn]['types'][0] == 'country') {
                        $tmp['country'] = $vn['long_name'];
                        continue;
                    }
                }
            }
        }

        if (!empty($tmp)) {
            $data['country'] = $tmp['country'];
            $data['province'] = isset($tmp['administrative_area_level_1']) ? $tmp['administrative_area_level_1'] : isset($tmp['locality']) ? $tmp['locality'] : '';
            $data['city'] = isset($tmp['locality']) ? $tmp['locality'] : isset($tmp['neighborhood']) ? $tmp['neighborhood'] : '';
        } else {
            $data['county'] = '海外';
            $data['province'] = '';
            $data['city'] = '';
        }
        return $data;
    }

}
