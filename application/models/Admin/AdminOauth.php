<?php
namespace Admin;
class AdminOauthModel extends \Core\BaseModels {
 
    public function adminLogin($oauthData){
        if(empty($oauthData['account']) || empty($oauthData['password'])|| strlen($oauthData['account'])<=3 || strlen($oauthData['account'])>20){
            return $this->returnResult(401);
        }

        $options['table'] = 'admin';
        $options['where'] = array('admin_name'=>'?');
        // $options['field'] = 'admin_id,admin_name,cellphone,register_time';
        $options['param']=array($oauthData['account']);
        $userOauth = $this->db->find($options);
        if(!empty($userOauth)){
            if($userOauth['password'] !== md5($oauthData['password'])){
                return $this->returnResult(402,array('Wrong Password'));
            }
        }else{
            return $this->returnResult(201,array('Account Not Exist'));
        }
        unset($oauthData['password']);
        $userOauth['register_time'] = date('Y:m:d H:i:s',$userOauth['register_time']);
        return $this->returnResult(200,$userOauth);
    }
}


