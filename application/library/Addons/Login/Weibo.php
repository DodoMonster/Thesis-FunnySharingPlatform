<?php
namespace Addons\Login;
include_once('weibo/saetv2.ex.class.php');
class Weibo {
    public $_SaeTOAuthV2;
    public $_SaeTClientV2;
    private $_wb_akey;//TODO写入配置文件
    private $_wb_skey;
    private $_wb_callback_url;
    public function __construct($access_token = '') {
        $this->_wb_akey = "3120423432";
        $this->_wb_skey = "";
        $this->_wb_callback_url='';
        if (!empty($access_token)) {
            $this->_SaeTClientV2 = new \SaeTClientV2($this->_wb_akey, $this->_wb_skey, $access_token);
        }else{
            $this->_SaeTOAuthV2 = new \SaeTOAuthV2( $this->_wb_akey , $this->_wb_skey);
        }
    }
    
    //获得code地址
    public function getCodeUrl(){
        return $this->_SaeTOAuthV2->getAuthorizeURL($this->_wb_callback_url);
    }
    
    //获得AccessToken
    public function getAccessToken($code){
        $keys['code'] = $code;
	$keys['redirect_uri'] = $this->_wb_callback_url;
        return $this->_SaeTOAuthV2->getAccessToken( 'code', $keys ) ;
        //Array ( [access_token] => 2.00YDXk_GMgyK6Dcc274251cb8Aw_hC [remind_in] => 157679999 [expires_in] => 157679999 [uid] => 5788637536 )
    }
    
    //获得用户信息
    public function getUserInfo($accessToken){
        if(isset($accessToken['access_token'])){
            $this->_SaeTClientV2 = new \SaeTClientV2($this->_wb_akey, $this->_wb_skey, $accessToken['access_token']);
            $me = $this->_SaeTClientV2->show_user_by_id($accessToken['uid']);
        }
        return $me;
    }
    //Array ( [id] => 5177682515 [idstr] => 5177682515 [class] => 1 [screen_name] => 15真实的一天 [name] => 15真实的一天 [province] => 61 [city] => 1 [location] => 陕西 西安 [description] => [url] => [profile_image_url] => http://tp4.sinaimg.cn/5177682515/50/5734256593/1 [cover_image_phone] => http://ww3.sinaimg.cn/crop.0.0.640.640.640/6ce2240djw1e9114p3hudj20hs0hs427.jpg [profile_url] => u/5177682515 [domain] => [weihao] => [gender] => m [followers_count] => 11 [friends_count] => 44 [pagefriends_count] => 2 [statuses_count] => 10 [favourites_count] => 4 [created_at] => Fri Jun 13 17:28:10 +0800 2014 [following] => [allow_all_act_msg] => [geo_enabled] => 1 [verified] => [verified_type] => -1 [remark] => [status] => Array ( [created_at] => Fri Jan 15 10:57:44 +0800 2016 [id] => 3.9315023397321E+15 [mid] => 3931502339732127 [idstr] => 3931502339732127 [text] => 转发微博 [source_allowclick] => 0 [source_type] => 1 [source] => Android客户端 [favorited] => [truncated] => [in_reply_to_status_id] => [in_reply_to_user_id] => [in_reply_to_screen_name] => [pic_urls] => Array ( ) [geo] => [annotations] => Array ( [0] => Array ( [client_mblogid] => 9b67fde2-4ff3-4e26-a961-5418602ed667 ) [1] => Array ( [mapi_request] => 1 ) ) [reposts_count] => 0 [comments_count] => 0 [attitudes_count] => 0 [isLongText] => [mlevel] => 0 [visible] => Array ( [type] => 0 [list_id] => 0 ) [biz_feature] => 0 [darwin_tags] => Array ( ) [hot_weibo_tags] => Array ( ) [userType] => 0 ) [ptype] => 0 [allow_all_comment] => 1 [avatar_large] => http://tp4.sinaimg.cn/5177682515/180/5734256593/1 [avatar_hd] => http://ww2.sinaimg.cn/crop.0.0.1080.1080.1024/005Ep1Kjjw8euyuuep86cj30u00u0abf.jpg [verified_reason] => [verified_trade] => [verified_reason_url] => [verified_source] => [verified_source_url] => [follow_me] => [online_status] => 0 [bi_followers_count] => 5 [lang] => zh-cn [star] => 0 [mbtype] => 0 [mbrank] => 0 [block_word] => 0 [block_app] => 0 [credit_score] => 80 [user_ability] => 0 [urank] => 9 )
}

