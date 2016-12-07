<?php
class Oauth2Controller extends \Core\BaseControllers {

    public function init() {
        parent::init();
        $clientId=isset($this->_postData['client_id'])?intval($this->_postData['client_id']):'';
        //$clientId=1000000003;
        $model=new \Addons\Oauth2\PDOOAuth2();
        $gameInfo=$model->getGameInfo($clientId);
        parent::verifySign($gameInfo['client_secret']);//签名正确
        $this->_clientId=$clientId;
        $this->_clientSecret=$gameInfo['client_secret'];
        unset($gameInfo['client_secret']);
        $this->_gameInfo=$gameInfo;
    }
    
    //TODO
    public function addClientAction(){
        //$model =new Users\UserOauth2Model();
        //$data=$model->addClient();
        //$this->returnValue($data);
    }
    
    //授权码
    public function authorizeCodeAction(){
        $model =new Users\UserOauth2Model();
        $data=$model->authorizeCode();
        $this->returnValue($data);
    }
    
    //授权页(需要确认和回调页)
    public function authorizeAction(){
        $post['accept']=isset($this->_postData['accept']) ? $this->_postData['accept']:'Yep';//Yep
        $model =new Users\UserOauth2Model();
        $data=$model->authorize($post);
        $this->returnValue($data);
    }
    
    //授权token(used)
    public function authorizeTokenAction(){
        $model =new Users\UserOauth2Model();
        $data=$model->authorizeToken();
        $this->returnValue($data);
    }
    
    //获得accesstoken//refreshtoken(used)
    public function grantAccessTokenAction(){
        $model =new Users\UserOauth2Model();
        $data=$model->grantAccessToken();
        $this->returnValue($data);
    }
    
    //验证accesstoken
    public function verifyAccessTokenAction(){
        //$model =new Users\UserOauth2Model();
        //$data=$model->verifyAccessToken();
        //$this->returnValue($data);
    }

    public function testLoginAction() {
        //setcookie('LOGGED_USER',session_id()."#".base64_encode(641),time()+3600*24*365,'/');
        //$_SESSION['uid']=641;
        echo str_replace("0.", "", str_replace(" ", "", microtime())) . "<br/>";
        echo microtime(true) . "<br/>";
        echo uniqid() . "<br/>";
        echo base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), uniqid())) . "<br/>";
        echo md5(base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), uniqid()))) . "<br/>";
        echo time() + mt_rand(1, 10000) . "<br/>";
        echo date("Y-m-d H:i:s", 2100000000) . "<br/>";
        echo date("Y-m-d H:i:s", 1460529192) . "<br/>";
        echo base_convert(uniqid(), 16, 10). "<br/>";
        echo time()+mt_rand(10000000,99999999)+1000000000;
        //1460529192
        //3757642997
        //1531478069838561
        //8069838561
    }

}
