<?php
class UserController extends \Core\BaseControllers {
     
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
        if($this->_mid<=0){
            \Core\BaseErrors::ErrorHandler(4039);
        }
    }
    
    //获得用户信息
    public function getUserInfoAction(){
        $uid=$this->_mid;
        $gameInfo=$this->_gameInfo;
        $oauthData['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: '';
        $oauthData['uname']=isset($this->_postData['uname']) ? $this->_postData['uname']: '';
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '';
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
        
        $model= new \Users\UserInfoModel();
        $data=$model->getUserInfo($uid,$gameInfo,$oauthData);
        $this->returnValue($data);
    }
    
    //修改密码
    public function resetPasswordAction(){
        $uid=$this->_mid;
        $password=isset($this->_postData['password']) ? $this->_postData['password']: '';
        $newPassword=isset($this->_postData['new_password']) ? $this->_postData['new_password']: '';
        $model= new \Users\UserInfoModel();
        $data=$model->resetPassword($uid,$password,$newPassword);
        $this->returnValue($data);
    }
    
    //绑定手机操作
    public function bindCellphoneAction(){
        $uid=$this->_mid;
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['msg_code']=isset($this->_postData['msg_code']) ? $this->_postData['msg_code']: '';
        $model= new \Users\UserInfoModel();
        $data=$model->bindCellphone($uid,$oauthData);
        $this->returnValue($data);
    }
    
    //解除绑定手机操作
    public function unbindCellphoneAction(){
        $uid=$this->_mid;
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['msg_code']=isset($this->_postData['msg_code']) ? $this->_postData['msg_code']: '';
        $model= new \Users\UserInfoModel();
        $data=$model->unbindCellphone($uid,$oauthData);
        $this->returnValue($data);
    }
    
    //绑定游戏账号
    public function bindGameAccountAction(){
        $uid=$this->_mid;
        $oauthData['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $oauthData['game_uname']=isset($this->_postData['game_uname']) ? $this->_postData['game_uname']: '';
        $oauthData['game_server']=isset($this->_postData['game_server']) ? $this->_postData['game_server']: '';
        
        $oauthData['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: '';
        $oauthData['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: '';
        $oauthData['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: '';
        $oauthData['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: '6';//TODO
        
        $oauthData['client_id']=$this->_clientId;
        
        $game=$this->_gameInfo;
        
        $model= new \Users\UserInfoModel();
        $data=$model->bindGameAccount($uid,$oauthData,$game);
        $this->returnValue($data);
    }
    
    //游戏创角
    public function createGameRoleAction(){
        $uid=$this->_mid;
        $gameInfo=$this->_gameInfo;
        $oauthData['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: '';
        $oauthData['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $oauthData['platform']=isset($this->_postData['platform']) ? $this->_postData['platform']: '';
        $oauthData['server_id']=isset($this->_postData['server_id']) ? $this->_postData['server_id']: '';
        $oauthData['server_name']=isset($this->_postData['server_name']) ? $this->_postData['server_name']: '';
        $oauthData['role_id']=isset($this->_postData['role_id']) ? $this->_postData['role_id']: '';
        $oauthData['role_name']=isset($this->_postData['role_name']) ? $this->_postData['role_name']: '';
        $model= new \Users\UserInfoModel();
        $data=$model->createGameRole($uid,$gameInfo,$oauthData);
        $this->returnValue($data);
    }
    
}

