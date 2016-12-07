<?php
namespace Admin;
class AdminOauthModel extends \Core\BaseModels {
 
    public function adminLogin($oauthData){
        if(empty($oauthData['account']) || empty($oauthData['password'])|| strlen($oauthData['account'])<=3 || strlen($oauthData['account'])>20){
            return $this->returnResult(401);
        }
        if(!isset($_SESSION['securimage_code_value']['admin']) || strtolower($oauthData['captcha'])!=$_SESSION['securimage_code_value']['admin']){
            return $this->returnResult(201);
        }
        //判断是手机登录还是用户登录
        $isRegCellphone=$this->isRegCellphone($oauthData['account']);
        if($isRegCellphone){//手机登录
            $options['table']='user_cellphone as A';
            $options['join']=array('user as B on A.uid=B.uid');
            $options['where']=array('A.cellphone'=>'?');
            $options['field']='B.uid,B.password,B.uname,B.privilege';
            $options['param']=array($oauthData['account']);
            $userOauth=$this->db->find($options);
            if(!empty($userOauth)){
                if($userOauth['password']!==md5($oauthData['password'])){
                    return $this->returnResult(402,array('Wrong Password'));
                }
                if($userOauth['privilege']<8){//todo角色表里
                    return $this->returnResult(402,array('Account Not Exist'));
                }
            }else{
                return $this->returnResult(402,array('Account Not Exist'));
            }
            unset($oauthData['password']);
        }else{//用户名登录
            $options['table']='user as A';
            $options['join']=array('user_cellphone as B on A.uid=B.uid');
            $options['where']=array('A.uname'=>'?');
            $options['field']='A.uid,A.password,A.uname,A.privilege';
            $options['param']=array($oauthData['account']);
            $userOauth=$this->db->find($options);
            if(!empty($userOauth)){
                if($userOauth['password']!==md5($oauthData['password'])){
                    return $this->returnResult(402,array('Wrong Password'));
                }
                if($userOauth['privilege']<8){//todo角色表里
                    return $this->returnResult(402,array('Account Not Exist'));
                }
            }else{
                return $this->returnResult(402,array('Account Not Exist'));
            }
            unset($oauthData['password']);
        }
        return $this->returnResult(200,$userOauth);
    }
}


