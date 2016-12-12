<?php
class thesisController extends \Core\BaseControllers {
    protected $_cdnUrl = '';
    protected $_pageTypes = array();
    protected $_gameId = '';
    protected $_package = '';
    protected $_domain = '';


    public function init() {
        parent::init();
        // $this->_cdnUrl = CDN_URL;
        // $this->_domain = $_SERVER['SERVER_NAME'];
        // $model = new \Web\DzzModel();
        // $data = $model->getGameInfo();
        // if($data['code']==201){
        //     $this->error404Action();
        // }

    }

    //用户登录
    public function loginAction(){
        $oauthData['username']=isset($this->_postData['username']) ? $this->_postData['username']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        // $oauthData['captcha']=isset($this->_postData['captcha']) ? $this->_postData['captcha']: '';
        // print_r($oauthData);
        $model = new \Web\ThesisModel();
        $data = $model->login($oauthData);

        if($data['code'] == 200){ 
            $this->setOauthAdminSession($data);
            $this->redirect("/thesis/home");die;
        }elseif($data['code'] == 201){
            echo "<script>alert ('验证码错误');history.back();</script>";die;
        }elseif($data['code'] == 401){
            echo "<script>alert('账号或密码长度有误');history.back();</script>";die;
        }elseif($data['code'] == 402){
            echo "<script>alert('账号不存在或密码错误');history.back();</script>";die;
        }
    }
    
    //平台登出
    public function opLogoutAction(){
        $this->unsetOauthAdminSession();
        $this->redirect("/admin/adminoauth/login");
    }
    //验证身份
    public function verifyIdentity(){
        if(ALLOW_OAUTH2){
            $model=new \Addons\Oauth2\PDOOAuth2();
            $token=$model->verifyAccessTokenJson();//oauth_token正确
            $gameInfo=$model->getGameInfo($token['client_id']);
            parent::verifySign($gameInfo['client_secret']);//签名正确
            $this->_clientId=$token['client_id'];
            $this->_clientSecret=$gameInfo['client_secret'];
            unset($gameInfo['client_secret']);
            $this->_gameInfo=$gameInfo;
        }
    }

    //页面不存在
    public function error404Action(){
        echo '页面不存在';exit;
    }

    // 首页
    public function homeAction(){
        $this->display('index');
    }
    
}