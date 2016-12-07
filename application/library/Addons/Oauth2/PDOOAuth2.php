<?php
namespace Addons\Oauth2;
require_once "lib/OAuth2.inc";
class PDOOAuth2 extends \OAuth2 {
    private $db;
    public function __construct() {
        parent::__construct();
        $config=\Yaf_Registry::get("dbconfig")->db->toArray();
        $name='';
        $connection='';
        $this->db = new \Core\Dao\Db\DbInit($config,$name,$connection);
    }
    public function __destruct() {
        $this->db = NULL; // Release db connection
    }
    
    //添加clientid
    /*public function addClient() {
        $uname=time()+mt_rand(10000000,99999999)+1000000000;
        $password=md5(rand(10000001,99999999));
        $clientSecret=md5(base64_encode(pack('N5',mt_rand(),mt_rand(), mt_rand(), mt_rand(), uniqid())));
        
        $tmpData=array('uname'=>'?','password'=>'?','privilege'=>'?','addtime'=>'?');
        $options=array('table'=>'user','param'=>array($uname,$password,2,time()));
        $this->db->startTrans();
        $clientId=$this->db->add($tmpData,$options);
        $tmpData1=array('client_id'=>'?','client_secret'=>'?','redirect_uri'=>'?');
        $options1=array('table'=>'oauth2_clients','param'=>array($clientId,$clientSecret,''));
        $status=$this->db->add($tmpData1,$options1);
        if($status!=FALSE&& $clientId!=FALSE){
            $this->db->commit();
            return array('client_id'=>$clientId,'client_secret'=>$clientSecret,'redirect_uri'=>'');
        }else{
            $this->db->rollback();
            return False;
        }
    }*/
    
    /*  
     *  获得game_id,client_secret;
     */
    public function getGameInfo($client_id){
        $options['table']='oauth2_clients as A';
        $options['join']='game as B on A.client_id=B.client_id';
        $options['field']='A.client_secret,B.game_id,B.game_name,B.game_table_name,B.home_uri,B.bbs_uri,B.remark';
        $options['where']=array('A.client_id'=>'?');
        $options['param']=array($client_id);
        $result=$this->db->find($options);
        if(empty($result)){
            \Core\BaseErrors::ErrorHandler(4006);
        }
        return $result;
    }
    
    /*  
     *  获得client秘钥
     */
    public function getClientSecret($client_id){
        $options['table']='oauth2_clients';
        $options['field']='client_secret';
        $options['where']=array('client_id'=>'?');
        $options['param']=array($client_id);
        $result=$this->db->find($options);
        return !empty($result)?$result["client_secret"]:'';
    }
    
    /*  
     *  检查client信用
     *  Implements OAuth2::checkClientCredentials().
     */
    protected function checkClientCredentials($client_id, $client_secret = '') {
        $options['table']='oauth2_clients';
        $options['field']='client_secret';
        $options['where']=array('client_id'=>'?');
        $options['param']=array($client_id);
        $result=$this->db->find($options);
        if($client_secret===NULL){
            return $result !== FALSE;
        }
        return $result["client_secret"] == $client_secret;
    }
    
    /*  
     *  获得回调地址
     *  Implements OAuth2::getRedirectUri().
     */
    protected function getRedirectUri($client_id) {
        $options['table']='oauth2_clients';
        $options['field']='redirect_uri';
        $options['where']=array('client_id'=>'?');
        $options['param']=array($client_id);
        $result=$this->db->find($options);
        if($result===FALSE){
            return FALSE;
        }
        return (isset($result["redirect_uri"]) && $result["redirect_uri"]) ? $result["redirect_uri"] : NULL;
    }
    
    /*  
     *  获得accesstoken
     *  Implements OAuth2::getAccessToken().
     */
    protected function getAccessToken($oauth_token) {
        $options['table']='oauth2_tokens';
        $options['field']='client_id, expires, scope';
        $options['where']=array('oauth_token'=>'?');
        $options['param']=array($oauth_token);
        $result=$this->db->find($options);
        return $result !== FALSE ? $result : NULL;
    }
    
    /*  
     *  获得accesstoken
     *  Implements OAuth2::getAccessTokenByClient().TODO
     */
    protected function getAccessTokenByClient($client_id,$client_secret=''){
        $options['table']='oauth2_tokens';
        $options['field']='client_id, expires, scope';
        $options['where']=array('client_id'=>'?','expries'=>array('gt','?'));
        $options['param']=array($client_id,time());
        $result=$this->db->find($options);
        return $result !== FALSE ? $result : NULL;
    }
    
    /*  
     *  生成accesstoken
     *  Implements OAuth2::setAccessToken().
     */
    protected function setAccessToken($oauth_token, $client_id, $expires, $scope = NULL) {
        /*$options1['table']='oauth2_tokens';
        $options1['where']=array('client_id'=>'?');
        $options1['param']=array($client_id);
        $this->db->startTrans();
        $status=$this->db->delete($options1);*/
        $tmpData=array('oauth_token'=>'?','client_id'=>'?','expires'=>'?','scope'=>'?');
        $options=array('table'=>'oauth2_tokens','param'=>array($oauth_token,$client_id,$expires,$scope));
        $status1=$this->db->add($tmpData,$options);
        /*if($status!==FALSE&&$status1!==FALSE){
            $this->db->commit();
        }else{
            $this->db->rollback();
        }*/
    }
    
    /*  
     *  获得更新token
     *  Implements OAuth2::getRefreshToken().
     */
    protected function getRefreshToken($refresh_token) {
        $options['table']='oauth2_refresh_token';
        $options['field']='client_id,token,expires, scope';
        $options['where']=array('token'=>'?');
        $options['param']=array($refresh_token);
        $result=$this->db->find($options);
        return $result !== FALSE ? $result : NULL;
    }
    
    /*  
     *  设置更新token
     *  Implements OAuth2::setRefreshToken().
     */
    protected function setRefreshToken($refresh_token, $client_id, $expires, $scope = NULL) {
        /*$options['table']='oauth2_refresh_token';
        $options['where']=array('client_id'=>'?');
        $options['param']=array($client_id);
        $this->db->startTrans();
        $status=$this->db->delete($options);*/
        $tmpData1=array('token'=>'?','client_id'=>'?','expires'=>'?','scope'=>'?');
        $options1=array('table'=>'oauth2_refresh_token','param'=>array($refresh_token,$client_id,$expires,$scope));
        $status1=$this->db->add($tmpData1,$options1);        
        /*if($status!==FALSE&&$status1!==FALSE){
            $this->db->commit();
        }else{
            $this->db->rollback();
        }*/
    }
    
    /*  
     *  
     *  Implements OAuth2::getSupportedGrantTypes().
     */
    protected function getSupportedGrantTypes() {
        return array(OAUTH2_GRANT_TYPE_AUTH_CODE,OAUTH2_GRANT_TYPE_REFRESH_TOKEN);
    }
    
    /*  
     *  获得授权码
     *  Implements OAuth2::getAuthCode().
     */
    protected function getAuthCode($code) {
        $options['table']='oauth2_auth_codes';
        $options['field']='code,client_id,redirect_uri,expires, scope';
        $options['where']=array('code'=>'?');
        $options['param']=array($code);
        $result=$this->db->find($options);
        return $result !== FALSE ? $result : NULL;
    }
    
    /*  
     *  生成授权码
     *  Implements OAuth2::setAuthCode().
     */
    protected function setAuthCode($code, $client_id, $redirect_uri, $expires, $scope = NULL) {
        $tmpData=array('code'=>'?','client_id'=>'?','redirect_uri'=>'?','expires'=>'?','scope'=>'?');
        $options=array('table'=>'oauth2_auth_codes','param'=>array($code,$client_id,$redirect_uri,$expires,$scope));
        $status=$this->db->add($tmpData,$options);
    }
}
