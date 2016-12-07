<?php
namespace Admin;
class AdminOperateModel extends \Core\BaseModels {
    
    //添加管理员账号
    public function opAddAdmin($oauthData){
        $options['table']='user';
        $options['where']=array('uname'=>'?');
        $options['param']=array($oauthData['uname']);
        $result=$this->db->find($options);
        if(!empty($result)){
            return $this->returnResult(201);
        }
        $tmpData1=array('uname'=>'?','password'=>'?','ip'=>'?','addtime'=>'?','privilege'=>'?');
        $options1['table']='user';
        $options1['param']=array($oauthData['uname'],md5($oauthData['password']),$oauthData['ip'],time(),$oauthData['role_id']);
        $status=$this->db->add($tmpData1,$options1);
        if($status!=FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }

    // 管理员编辑操作
    public function opAdminEdit($param){
        $options['table'] = 'user';
        $options['where'] = array('uid'=>'?');
        $options['param'] = array($param['uid']);
        $data = $this->db->find($options);
        if(!empty($data) && $data['privilege']=='9'){
            return $this->returnResult(201);
        }
        $tmpData = array('state'=>'?','privilege'=>'?');
        $options['table'] = 'user';
        $options['where'] = array('uid'=>'?');
        $options['param'] = array($param['state'],$param['role'],$param['uid']);
        $uid = $this->db->save($tmpData,$options);

        if($uid!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }
    
    //添加管理员角色账号
    public function opAddAdminRole($oauthData){
        $options['table']='sys_user_role';
        $options['where']=array('role_name'=>'?');
        $options['param']=array($oauthData['role_name']);
        $result=$this->db->find($options);
        if(!empty($result)){
            return $this->returnResult(201);
        }
        $tmpData1=array('role_name'=>'?','menus'=>'?','products'=>'?','channels'=>'?','columns'=>'?');
        $options1['table']='sys_user_role';
        $options1['param']=array($oauthData['role_name'],$oauthData['menus'],$oauthData['products'],$oauthData['channels'],$oauthData['columns']);
        $status=$this->db->add($tmpData1,$options1);
        if($status!=FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }

    // 编辑管理员角色
    public function opEditAdminRole($oauthData){
        $tmpData = array('role_name'=>'?','menus'=>'?','products'=>'?','channels'=>'?','columns'=>'?');
        $options['table'] = 'sys_user_role';
        $options['where'] = array('role_id'=>'?');
        $options['param'] = array($oauthData['role_name'],$oauthData['menus'],$oauthData['products'],$oauthData['channels'],$oauthData['columns'],$oauthData['role_id'],);
        $roleId = $this->db->save($tmpData,$options);
        if($roleId!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }
}


