<?php
namespace Addons\Tencent;
class WechatFwh{
    
    private $_appid='wx5933e89260ec6c4d';
    private $_appsecret='4697ab9b911590144c0781a3293ac7b5';
    private $_accesstoken=false;
    
    //get_userlist
    //https://api.weixin.qq.com/cgi-bin/user/get?access_token=TpEF7izkdvNVuf16paeZp-1rYgRIyusIl_sA4xpF6whWb0k5QPMGACPVpX9bwLaccB_xtgtZql_txAnjbiwGdpcipO12GrW_0kO_XYXc1j4&next_openid=NEXT_OPENID
    //{"errcode":42001,"errmsg":"access_token expired hint: [QeUFuA0468vr23]"}
    
    //get_accesstoken
    //https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx5933e89260ec6c4d&secret=4697ab9b911590144c0781a3293ac7b5
    //{"access_token":"TpEF7izkdvNVuf16paeZp-1rYgRIyusIl_sA4xpF6whWb0k5QPMGACPVpX9bwLaccB_xtgtZql_txAnjbiwGdpcipO12GrW_0kO_XYXc1j4","expires_in":7200}
    
    
    //get_userinfo
    //https://api.weixin.qq.com/cgi-bin/user/info?access_token=ESFswPTdjKvZV15EKahX5beA4snEXQF2-SsSsWxgbnFvKv8tHqpBtHRgSWD3q2je40ijGcouW8ZGcjVbWZbAZf_N0jCkTSQ0HuEDnUMl0gU&openid=oRjhmxGPNaeO4j5YwpM812yFax0E&lang=zh_CN
    //{"subscribe":1,"openid":"oRjhmxGPNaeO4j5YwpM812yFax0E","nickname":"閽熷缓杈�","sex":1,"language":"zh_CN","city":"娣卞湷","province":"骞夸笢","country":"涓浗","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/Q3auHgzwzM6EjHTTGWyExtj4slHxz0P8GkXfdqphKJickGzxCgop59ibjbp630qYkBRoPbXROAoXf5dpQOiccfYfMtmlnI6CahrWXyUlYWGut8\/0","subscribe_time":1442151024,"remark":"","groupid":0}
    
    public function __construct() {
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->_appid.'&secret='.$this->_appsecret;
        $result=\Addons\Grab\Grab::single_grab_json($url);
        $this->_accesstoken=isset($result['access_token'])?$result['access_token']:false;
    }

    public function getAccessToken(){
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->_appid.'&secret='.$this->_appsecret;
        $result=\Addons\Grab\Grab::single_grab_json($url);
        $this->_accesstoken=isset($result['access_token'])?$result['access_token']:false;
    }
    
    public function getFollowUserList($nextOpenid=''){
        $url='https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->_accesstoken.'&next_openid='.$nextOpenid;
        $result=\Addons\Grab\Grab::single_grab_json($url);
        return $result;
    }
    
    public function getUserInfo($openid){
        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->_accesstoken.'&openid='.$openid.'&lang=zh_CN';
        $result=\Addons\Grab\Grab::single_grab_json($url);
        return $result;
    }
    
    public function getUserInfoList($openids){
        $nodes=array();
        foreach ($openids as $v){
            $nodes[]='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->_accesstoken.'&openid='.$v.'&lang=zh_CN';
        }
        $result=\Addons\Grab\Grab::multiple_grab_json($nodes);
        return $result;
        
    }
}

