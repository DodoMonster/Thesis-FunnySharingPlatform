<?php

namespace Addons\Login;

class Taobao {

    private $_AppKey; //TODO写入配置文件
    private $_AppSecret;
    private $_CallbackUrl;
    private $_DevCodeServer = 'https://oauth.tbsandbox.com/authorize';
    private $_DevTokenServer = 'https://oauth.tbsandbox.com/token';
    private $_ProCodeServer = 'https://oauth.taobao.com/authorize';
    private $_ProTokenServer = 'https://oauth.taobao.com/token';

    public function __construct($access_token = '') {
        $this->_AppKey = "23275226";
        $this->_AppSecret = "";
        $this->_CallbackUrl = '';
        //23287770
        //20b84bfd283e491a068a00f08ec0bac5
    }

    /* （1）获取授权码（code）
      正式环境：https://oauth.taobao.com/authorize
      沙箱环境：https://oauth.tbsandbox.com/authorize

      （2）获取访问令牌（access_token）
      正式环境：https://oauth.taobao.com/token
      沙箱环境：https://oauth.tbsandbox.com/token
     */

    //https://oauth.taobao.com/authorize?response_type=code&client_id=23075594&redirect_uri=http://www.oauth.net/2/&state=1212&view=web
    //curl -i -d "code=OxlukWofLrB1Db1M6aJGF8x2332458&grant_type=authorization_code&client_id=23075594&client_secret=69a1469a1469a1469a14a9bf269a14&redirect_uri=http://www.oauth.net/2/ " https://oauth.taobao.com/token
    //https://oauth.taobao.com/token?code=OxlukWofLrB1Db1M6aJGF8x2332458&grant_type=authorization_code&client_id=23075594&client_secret=69a1469a1469a1469a14a9bf269a14&redirect_uri=http://www.oauth.net/2/
    //获得code地址
    public function getCodeUrl() {
        //TODOstate为随机数加session值
        //return "$this->_DevCodeServer?response_type=code&client_id=$this->_AppKey&redirect_uri=$this->_CallbackUrl&state=1212&view=web";
        return "$this->_ProCodeServer?response_type=code&client_id=$this->_AppKey&redirect_uri=" . urlencode($this->_CallbackUrl) . "&state=1212&view=web";
    }

    //获得AccessToken
    public function getAccessToken($code) {
        //$url="$this->_DevTokenServer?code=$code&grant_type=authorization_code&client_id=$this->_AppKey&client_secret=$this->_AppSecret&redirect_uri=$this->_CallbackUrl";
        //$url="$this->_ProTokenServer?code=$code&grant_type=authorization_code&client_id=$this->_AppKey&client_secret=$this->_AppSecret&redirect_uri=$this->_CallbackUrl";
        //return \Addons\Grab\Grab::single_grab_json($url);
        $url = $this->_ProTokenServer;
        $postfields = array('grant_type' => 'authorization_code', 'client_id' => $this->_AppKey, 'client_secret' => $this->_AppSecret, 'code' => $code, 'redirect_uri' => $this->_CallbackUrl);
        return \Addons\Grab\Grab::single_grab_json_postdata($url, $postfields);
        //Array ( [taobao_user_nick] => %E7%94%B7%E6%90%ADmenda [re_expires_in] => 86400 [expires_in] => 86400 [expire_time] => 1452760665234 [r1_expires_in] => 86400 [w2_valid] => 1452760665234 [w2_expires_in] => 86400 [w1_expires_in] => 86400 [r1_valid] => 1452760665234 [r2_valid] => 1452760665234 [w1_valid] => 1452760665234 [r2_expires_in] => 86400 [token_type] => Bearer [refresh_token] => 6200f26b7c25189f31421f0caa16bf8457ZZ496b75483ee2706157079 [open_uid] => AAFJX2YSACJX-szOb2rbkSA- [refresh_token_valid_time] => 1452760665234 [access_token] => 620002617a6fc18e51af4557550803a49fhj2616de2289f2706157079 )
    }

    //获得用户信息
    public function getUserInfo($accessToken) {
        $url = 'https://eco.taobao.com/router/rest?app_key=12129701&format=json&method=taobao.user.buyer.get&sign=6E4E3665E14CA63C521680070C57A778&sign_method=hmac&timestamp=2016-02-22+17%3A54%3A28&v=2.0&fields=nick%2Csex';
        //return \Addons\Grab\Grab::single_grab_json($url);
        //{"user_buyer_get_response":{"user":{"nick":"hz0799","sex":"m","avatar":"http:\/\/assets.taobaocdn.com\/app\/sns\/img\/default\/avatar-120.png"}}}
    }

    //Client-side flow
    //https://oauth.taobao.com/authorize?response_type=token&client_id=23075594&state=1212&view=web
    //https://oauth.taobao.com/oauth2?view=web#access_token=6102b10b4a7609f8ab197ff7bc717279b56ac3a3970b3fb2706157079&token_type=Bearer&expires_in=86400&refresh_token=61018109e293f4d63d1da8c4e18ce13a3e41167388bd79f2706157079&re_expires_in=86400&r1_expires_in=86400&r2_expires_in=86400&taobao_user_nick=%E7%94%B7%E6%90%ADmenda&w1_expires_in=86400&w2_expires_in=1800&state=1212&top_sign=AAB5D80024FCF073D70D9A7C0D157507
    //hash_hmac('md5', $data, $secret);
    public function sentSMS() {
        $cellphone = 13560235745;
        $sms = json_encode(array("code" => "1234", "product" => "男搭"));
        $smsTempleteCode = 'SMS_6350035';
        $datetime = date("Y-m-d H:i:s", time());
        $url = "https://eco.taobao.com/router/rest";
        $postfields = array('app_key' => $this->_AppKey, 'format' => 'json', 'method' => 'alibaba.aliqin.fc.sms.num.send', 'sign_method' => 'md5', 'timestamp' => $datetime, 'v' => '2.0', 'rec_num' => $cellphone, 'sms_free_sign_name' => '大鱼测试', 'sms_param' => $sms, 'sms_template_code' => $smsTempleteCode, 'sms_type' => 'normal');
        /*$signString = '';
        ksort($postfields);
        foreach ($postfields as $k => $v) {
            $signString.="$k$v";
        }
        $sign = strtoupper(md5($this->_AppSecret . $signString . $this->_AppSecret));
        echo $sign;*/
        $sign=$this->generateSign($postfields);
        $postfields = array_merge($postfields, array('sign' => $sign));
        //$url="https://eco.taobao.com/router/rest?app_key=$this->_AppKey&format=json&method=alibaba.aliqin.fc.sms.num.send&sign=$this->_AppSecret&sign_method=hmac&timestamp=$datetime&v=2.0&rec_num=$cellphone&sms_free_sign_name=大鱼测试&sms_param=$sms&sms_template_code=$smsTempleteCode&sms_type=normal";
        $data = \Addons\Grab\Grab::single_grab_json_postdata($url, $postfields);
        print_r($data);
        exit;
        /* $c = new TopClient;
          $c->appkey = $appkey;
          $c->secretKey = $secret;
          $req = new AlibabaAliqinFcSmsNumSendRequest;
          $req->setExtend("123456");
          $req->setSmsType("normal");
          $req->setSmsFreeSignName("阿里大鱼");
          $req->setSmsParam("{\"code\":\"1234\",\"product\":\"alidayu\"}");
          $req->setRecNum("13000000000");
          $req->setSmsTemplateCode("SMS_585014");
          $resp = $c->execute($req); */
    }

    //生成签名
    protected function generateSign($params) {
        ksort($params);
        $stringToBeSigned = $this->secretKey;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->secretKey;
        return strtoupper(md5($stringToBeSigned));
    }

}
