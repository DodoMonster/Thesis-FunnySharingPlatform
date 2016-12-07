<?php
namespace Addons\Login;
class Wechat {

    private $_AppID;//TODO写入配置文件
    private $_AppSecret;
    private $_CallbackUrl;
    public function __construct($access_token = '') {
        $this->_AppID = "wx51c8f046470e0909";
        $this->_AppSecret = "";
        $this->_CallbackUrl='';
        
    }
    
    //获得code地址
    public function getCodeUrl(){
        //TODOstate为随机数加session值
        return "https://open.weixin.qq.com/connect/qrconnect?appid=$this->_AppID&redirect_uri=".urlencode($this->_CallbackUrl)."&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
    }
    
    //获得AccessToken
    public function getAccessToken($code){
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->_AppID&secret=$this->_AppSecret&code=$code&grant_type=authorization_code";
        return \Addons\Grab\Grab::single_grab_json($url);
        //Array ( [access_token] => OezXcEiiBSKSxW0eoylIeFAbNWyg6rAHBTuid62UT_-ut7qc1WQPFgy0XPZKvWt5Bj21A4u8d_O5Vcct3Z20d6cBLyhoDAHaQHPwjF6stB6yRo1kNerYK_5yhcmLROyWyFY8rSKEqRKXMFF2mfayRA [expires_in] => 7200 [refresh_token] => OezXcEiiBSKSxW0eoylIeFAbNWyg6rAHBTuid62UT_-ut7qc1WQPFgy0XPZKvWt5Tavd-uyA60bkS7h7i35IsEKLMpGgZ0iSJlbksmUljmDIBg3F5TV1ZSdQ_X5h1F1fR_CKXzW3LohjXyRNlJZsZQ [openid] => opdais8FOgkuB6Mp5saAl9hhNyTo [scope] => snsapi_login [unionid] => o98wZv14REEixP1PUsjJ-XUBYEU4 )
    }
    
    //获得用户信息
    public function getUserInfo($accessToken,$typeUid){
        $url="https://api.weixin.qq.com/sns/userinfo?access_token=$accessToken&openid=$typeUid";
        return \Addons\Grab\Grab::single_grab_json($url);
        //Array ( [openid] => opdais8FOgkuB6Mp5saAl9hhNyTo [nickname] => 乘二 [sex] => 0 [language] => zh_CN [city] => [province] => [country] => CN [headimgurl] => http://wx.qlogo.cn/mmopen/Q3auHgzwzM5fibPpOPP7a1Ub4rIGwZIqBLiajYx0aezWqxBTJH6ia0mpIXj0HiaN94b7Cz6ywJ5fYy8PvPiaUIL8VARyjbLCnpfxRzLqGdot6Unc/0 [privilege] => Array ( ) [unionid] => o98wZv14REEixP1PUsjJ-XUBYEU4 )
    }

}

