<?php
namespace Users;
class UserOauth2Model extends \Core\BaseModels {
    
    //添加clientid
    public function addClient() {
        $uname=time()+mt_rand(10000000,99999999)+1000000000;
        //$password=md5(rand(10000001,99999999));
        $password=md5(1234567);
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
            return $this->returnResult(200,array('client_id'=>$clientId,'client_secret'=>$clientSecret,'redirect_uri'=>''));
        }else{
            $this->db->rollback();
            return $this->returnResult(4000);
        }
    }
    
    //修改clientid,info['client_secret'],info['redirect_uri']
    public function saveClient($client_id,$info){
        $tmpData=array();
        $options['param']=array();
        $keys=array('client_secret','redirect_uri');
        if($info){
            foreach ($info as $k=>$v){
                if(!empty($v) && in_array($k,$keys)){
                    $tmpData=array_merge($tmpData,array($k=>'?'));
                    $options['param']= array_merge($options['param'],$v);
                }
            }
        }
        $options['table']='oauth2_clients';
        $options['where']=array('client_id'=>'?');
        $options['param']=array_merge($options['param'],array($client_id));
        $status=$this->db->save($tmpData,$options);
        if($status!=FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }
    
    //第三方授权页
    public function authorize($post){
        $model=new \Addons\Oauth2\PDOOAuth2();
        $result=$model->getAuthorizeParams();
        if($post['accept']=='Yep'){
            $model->finishClientAuthorization(true, $result);
        }
        if(!empty($result)){
            return $this->returnResult(200,$result);
        }else{
            return $this->returnResult(4000);
        }
    }
    
    //获得授权码
    public function authorizeCode(){
        $model=new \Addons\Oauth2\PDOOAuth2();
        $result=$model->getAuthorizeParams();
        $data=$model->finishClientAuthorization(true, $result);
        if(!empty($data)){
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(4000);
        }
    }
    
    //获得授权token
    public function authorizeToken(){
        $model=new \Addons\Oauth2\PDOOAuth2();
        $result=$model->getAuthorizeParams();
        $data=$model->finishClientAuthorizationNR(true, $result);
        return $this->returnResult(200,$data);
    }
    
    //获得accesstoken
    public function grantAccessToken(){
        $model=new \Addons\Oauth2\PDOOAuth2();
        $result=$model->grantAccessToken();
        return $this->returnResult(200,$result);
    }
    
    //验证accesstoken//TODO
    public function verifyAccessToken(){
        $model=new \Addons\Oauth2\PDOOAuth2();
        return $model->verifyAccessTokenJson();      
    }
}
