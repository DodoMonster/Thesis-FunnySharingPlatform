<?php
class OauthController extends \Core\BaseControllers {
    
    public function init() {
        parent::init();        
        //验证oauth_token是否正确,$_POST['oauth_token'];
        if(ALLOW_OAUTH2){
            $model=new \Addons\Oauth2\PDOOAuth2();
            $token=$model->verifyAccessTokenJson();//oauth_token正确
            //$clientSecret=$model->getClientSecret($token['client_id']);
            $gameInfo=$model->getGameInfo($token['client_id']);
            parent::verifySign($gameInfo['client_secret']);//签名正确
            $this->_clientId=$token['client_id'];
            $this->_clientSecret=$gameInfo['client_secret'];
            unset($gameInfo['client_secret']);
            $this->_gameInfo=$gameInfo;
        }
    }    
    
    //平台登出
    public function logoutAction(){
        $mid=$this->_mid;
        $oauthData['user_agent']=$this->_userAgent;
        $model= new \Users\UserOauthModel();
        $data=$model->logout($mid,$oauthData);
        if($data['code']==200){
            $this->unsetOauthSession();
        }
        $this->returnValue($data);       
    }
    
    //本地登录
    public function localLoginAction(){        
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '';
        $oauthData['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']: '';
        $oauthData['oauth_token']=isset($this->_postData['oauth_token']) ? $this->_postData['oauth_token']: '';
        
        $oauthData['user_agent']=$this->_userAgent;
        $oauthData['http_user_agent']=$this->_httpUserAgent;
        $oauthData['ip']=$this->_realIp;
        
        $oauthData['server_id']=isset($this->_postData['serverId']) ? $this->_postData['serverId']: '';
        $oauthData['os']=isset($this->_postData['os']) ? $this->_postData['os']: '';
        $oauthData['os_version']=isset($this->_postData['osVersion']) ? $this->_postData['osVersion']: '';
        $oauthData['device']=isset($this->_postData['device']) ? $this->_postData['device']: '';
        $oauthData['devicetype']=isset($this->_postData['deviceType']) ? $this->_postData['deviceType']: '';
        $oauthData['screen']=isset($this->_postData['screen']) ? $this->_postData['screen']: '';
        $oauthData['mno']=isset($this->_postData['mno']) ? $this->_postData['mno']: '';
        $oauthData['nm']=isset($this->_postData['nm']) ? $this->_postData['nm']: '';
        $oauthData['app_version']=isset($this->_postData['appVersion']) ? $this->_postData['appVersion']: '';
        $oauthData['sdk_version']=isset($this->_postData['sdkVersion']) ? $this->_postData['sdkVersion']: '';
        $model= new \Users\UserOauthModel();
        $data=$model->localLogin($oauthData);
        $this->setOauthSession($data);
        $this->returnValue($data);
    }
    
    //用户名注册
    public function registerViaUnameAction(){
        $oauthData['account']=isset($this->_postData['account']) ? strtolower($this->_postData['account']): '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '';
        $oauthData['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']: '';
        
        $oauthData['http_user_agent']=$this->_httpUserAgent;
        $oauthData['user_agent']=$this->_userAgent;
        $oauthData['ip']=$this->_realIp;
        
        $oauthData['server_id']=isset($this->_postData['serverId']) ? $this->_postData['serverId']: '';
        $oauthData['os']=isset($this->_postData['os']) ? $this->_postData['os']: '';
        $oauthData['os_version']=isset($this->_postData['osVersion']) ? $this->_postData['osVersion']: '';
        $oauthData['device']=isset($this->_postData['device']) ? $this->_postData['device']: '';
        $oauthData['devicetype']=isset($this->_postData['deviceType']) ? $this->_postData['deviceType']: '';
        $oauthData['screen']=isset($this->_postData['screen']) ? $this->_postData['screen']: '';
        $oauthData['mno']=isset($this->_postData['mno']) ? $this->_postData['mno']: '';
        $oauthData['nm']=isset($this->_postData['nm']) ? $this->_postData['nm']: '';
        $oauthData['app_version']=isset($this->_postData['appVersion']) ? $this->_postData['appVersion']: '';
        $oauthData['sdk_version']=isset($this->_postData['sdkVersion']) ? $this->_postData['sdkVersion']: '';
        $oauthData['game_id']=$this->_gameInfo['game_id'];
        $model= new \Users\UserOauthModel();
        $data=$model->registerViaUname($oauthData);
        $this->setOauthSession($data);
        $this->returnValue($data);
    }
    
    //手机注册
    public function registerViaPhoneAction(){
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        $oauthData['msg_code']=isset($this->_postData['msg_code']) ? $this->_postData['msg_code']: '';
        
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '';
        $oauthData['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']: '';
        
        $oauthData['http_user_agent']=$this->_httpUserAgent;
        $oauthData['user_agent']=$this->_userAgent;
        $oauthData['ip']=$this->_realIp;
        
        $oauthData['server_id']=isset($this->_postData['serverId']) ? $this->_postData['serverId']: '';
        $oauthData['os']=isset($this->_postData['os']) ? $this->_postData['os']: '';
        $oauthData['os_version']=isset($this->_postData['osVersion']) ? $this->_postData['osVersion']: '';
        $oauthData['device']=isset($this->_postData['device']) ? $this->_postData['device']: '';
        $oauthData['devicetype']=isset($this->_postData['deviceType']) ? $this->_postData['deviceType']: '';
        $oauthData['screen']=isset($this->_postData['screen']) ? $this->_postData['screen']: '';
        $oauthData['mno']=isset($this->_postData['mno']) ? $this->_postData['mno']: '';
        $oauthData['nm']=isset($this->_postData['nm']) ? $this->_postData['nm']: '';
        $oauthData['app_version']=isset($this->_postData['appVersion']) ? $this->_postData['appVersion']: '';
        $oauthData['sdk_version']=isset($this->_postData['sdkVersion']) ? $this->_postData['sdkVersion']: '';
        $oauthData['game_id']=$this->_gameInfo['game_id'];
        $model= new \Users\UserOauthModel();
        $data=$model->registerViaPhone($oauthData);
        $this->setOauthSession($data);
        $this->returnValue($data);
    }
    
    //一键试玩，自动注册
    public function autoRegisterAction(){
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '';
        $oauthData['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']: '';
        
        $oauthData['http_user_agent']=$this->_httpUserAgent;
        $oauthData['user_agent']=$this->_userAgent;
        $oauthData['ip']=$this->_realIp;
        
        $oauthData['server_id']=isset($this->_postData['serverId']) ? $this->_postData['serverId']: '';
        $oauthData['os']=isset($this->_postData['os']) ? $this->_postData['os']: '';
        $oauthData['os_version']=isset($this->_postData['osVersion']) ? $this->_postData['osVersion']: '';
        $oauthData['device']=isset($this->_postData['device']) ? $this->_postData['device']: '';
        $oauthData['devicetype']=isset($this->_postData['deviceType']) ? $this->_postData['deviceType']: '';
        $oauthData['screen']=isset($this->_postData['screen']) ? $this->_postData['screen']: '';
        $oauthData['mno']=isset($this->_postData['mno']) ? $this->_postData['mno']: '';
        $oauthData['nm']=isset($this->_postData['nm']) ? $this->_postData['nm']: '';
        $oauthData['app_version']=isset($this->_postData['appVersion']) ? $this->_postData['appVersion']: '';
        $oauthData['sdk_version']=isset($this->_postData['sdkVersion']) ? $this->_postData['sdkVersion']: '';
        $oauthData['game_id']=$this->_gameInfo['game_id'];
        $model= new \Users\UserOauthModel();
        $data=$model->autoRegister($oauthData);
        $this->setOauthSession($data);
        $this->returnValue($data);
    }
    
    //获得图片验证码
    public function getCaptchaAction(){
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['account']=session_id();
        $model= new \Users\UserOauthModel();
        $model->getCaptcha($oauthData);
        //session_id()
        //print_r($_SESSION);
    }
    
    //检验图片验证码（并发送手机验证码）
    public function checkCaptchaAction(){
        $oauthData['captcha_code']=isset($this->_postData['captcha_code']) ? strtolower($this->_postData['captcha_code']): '';
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['session_id']=session_id();
        $model= new \Users\UserOauthModel();
        $data=$model->checkCaptcha($oauthData);
        $this->returnValue($data);
    }
          
    //短信验证码
    public function sentSmsCodeAction(){
        $cellphone=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $model= new \Users\UserOauthModel();
        $data=$model->sentSmsCode($cellphone);
        $this->returnValue($data);
    }

    //用户是否注册
    public function isRegisterAccountAction(){
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $model= new \Users\UserOauthModel();
        $data=$model->isRegisterCellphone($oauthData);
        $this->returnValue($data);
    }
    
    //忘记密码
    public function forgetPasswordAction(){
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        $oauthData['msg_code']=isset($this->_postData['msg_code']) ? $this->_postData['msg_code']: '';
        $model= new \Users\UserOauthModel();
        $data=$model->forgetPassword($oauthData);
        $this->returnValue($data);
    }
    
    //第三方平台授权绑定自己平台账号
    public function oauthBindAccountAction(){
        $oauthData['oauth_uid']=isset($this->_postData['oauth_uid']) ? $this->_postData['oauth_uid']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '';
        $oauthData['token']=isset($this->_postData['token']) ? $this->_postData['token']: '';
        $oauthData['user_token']=isset($this->_postData['user_token']) ? $this->_postData['user_token']: '';
        
        $oauthData['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $oauthData['game_uname']=isset($this->_postData['game_uname']) ? $this->_postData['game_uname']: '';
        $oauthData['game_server']=isset($this->_postData['game_server']) ? $this->_postData['game_server']: '';
        
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']: '';
        
        $oauthData['http_user_agent']=$this->_httpUserAgent;
        $oauthData['user_agent']=$this->_userAgent;
        $oauthData['ip']=$this->_realIp;
        
        $oauthData['client_id']=$this->_clientId;
        $game=$this->_gameInfo;
        
        //查看授权用户id是否存在，
        //生成自己账号，绑定授权账号，绑定游戏账号，
        $model= new \Users\UserOauthModel();
        $data=$model->oauthBindAccount($oauthData,$game);
        $this->returnValue($data);
    }
    
    //获取第三方平台账号信息
    public function getOauthUserInfoAction(){
        $oauthData['oauth_uid']=isset($this->_postData['oauth_uid']) ? $this->_postData['oauth_uid']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '';
        $oauthData['token']=isset($this->_postData['token']) ? $this->_postData['token']: '';
        $oauthData['user_token']=isset($this->_postData['user_token']) ? $this->_postData['user_token']: '';
        
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['client_id']=$this->_clientId;
        $oauthData['http_user_agent']=$this->_httpUserAgent;
        $oauthData['user_agent']=$this->_userAgent;
        $oauthData['ip']=$this->_realIp;
        $game=$this->_gameInfo;
        
        $model= new \Users\UserOauthModel();
        $data=$model->getOauthUserInfo($oauthData,$game);
        $this->returnValue($data);
    }
    
    //认证用户信息
    public function verifyUserInfoAction(){
        $gameInfo=$this->_gameInfo;
        $oauthData['verify_token']=isset($this->_postData['verify_token']) ? $this->_postData['verify_token']: '';
        $oauthData['oauth_token']=isset($this->_postData['oauth_token']) ? $this->_postData['oauth_token']: '';
        $model= new \Users\UserInfoModel();
        $data=$model->verifyUserInfo($oauthData,$gameInfo);
        $this->returnValue($data);
    }
    
    //生成短连接（todo移除）
    public function addPromoteLinkAction(){
        $oauthData['gameflag']=isset($this->_postData['gameflag']) ? $this->_postData['gameflag']: (isset($this->_getData['gameflag']) ? $this->_getData['gameflag']:'');
        $oauthData['linkno']=isset($this->_postData['linkno']) ? $this->_postData['linkno']: (isset($this->_getData['linkno']) ? $this->_getData['linkno']:'');
        $oauthData['platformid']=isset($this->_postData['platformid']) ? $this->_postData['platformid']: (isset($this->_getData['platformid']) ? $this->_getData['platformid']:'');
        $oauthData['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: (isset($this->_getData['uid']) ? $this->_getData['uid']:'');
        $oauthData['server_id']=isset($this->_postData['server_id']) ? $this->_postData['server_id']: (isset($this->_getData['server_id']) ? $this->_getData['server_id']:'');
        $oauthData['server_name']=isset($this->_postData['server_name']) ? $this->_postData['server_name']: (isset($this->_getData['server_name']) ? $this->_getData['server_name']:'');
        $oauthData['role_id']=isset($this->_postData['role_id']) ? $this->_postData['role_id']: (isset($this->_getData['role_id']) ? $this->_getData['role_id']:'');
        $oauthData['role_name']=isset($this->_postData['role_name']) ? $this->_postData['role_name']: (isset($this->_getData['role_name']) ? $this->_getData['role_name']:'');
        $oauthData['role_school']=isset($this->_postData['role_school']) ? $this->_postData['role_school']: (isset($this->_getData['role_school']) ? $this->_getData['role_school']:'');
        $oauthData['role_shape']=isset($this->_postData['role_shape']) ? $this->_postData['role_shape']: (isset($this->_getData['role_shape']) ? $this->_getData['role_shape']:'');
        $oauthData['role_grade']=isset($this->_postData['role_grade']) ? $this->_postData['role_grade']: (isset($this->_getData['role_grade']) ? $this->_getData['role_grade']:'');
        $oauthData['ip']=$this->_realIp;
        $model= new \Users\UserInfoModel();
        $data=$model->addPromoteLink($oauthData);
        $this->returnValue($data);
    }
    
    //ios更新包
    public function updateIosAction(){
        $post['version_code']=isset($this->_postData['version_code']) ? $this->_postData['version_code']: (isset($this->_getData['version_code']) ? $this->_getData['version_code']:'');
        $model= new \Users\UserOauthModel();
        $data=$model->getIosVersion($post);
        $this->returnValue($data);
    }

    // 强行删除用户的手机绑定
    public function deleteBindCellphoneAction(){
        $oauthData['account']=isset($this->_postData['account'])?$this->_postData['account']:'';
        $model= new \Users\UserOauthModel();
        $data = $model->deleteBindCellphone($oauthData);
        echo json_encode($data);exit;
    }
}

