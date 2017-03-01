<?php
namespace Admin;
class AdminModel extends \Core\BaseModels {
    
    //获取用户信息
    public function getAdminInfo($aid){
        $options['table']='admin';
        $options['field']='admin_id,admin_name';
        $options['where'] = array('is_delete'=>'?','admin_id');
        $options['param'] = array('0',$aid);

        $list = $this->db->select($options);
        if(!empty($list)){
            return $this->returnResult(200,$list);
        }else{
            return $this->returnResult(201);
        }
    }

    //用户列表
    public function userList($uname,$count,$page){
        $options['table']='user';
        $options['field']='user_id,user_name,register_time';
        $options['where'] = array('is_delete'=>'?');
        $options['param'] = array('0');
        if(!empty($uname)){
            $options['where'] = array_merge($options['where'] = array('user_name'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'] = array("%{$uname}%"));
        }
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $options['limit']=($page-1)*$count.','.$count;
        // $options['order']='register_time DESC';
        $list = $this->db->select($options);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['register_time'] = date('Y-m-d H:i:s',$v['register_time']);
                // $list[$k]['privilege'] = $v['privilege']==0?'普通':($v['privilege']==2?'游戏商户':($v['privilege']>=6?'管理员':'其他'));
                // $list[$k]['state'] = $v['state']==0?'正常':'禁封';
            }
            $data=array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 删除用户操作
    public function deleteUser($uid){
        $options['table'] = 'user';
        $tmpData = array('is_delete'=>'?');                
        $options['where'] = array('user_id'=>'?');
        $options['param'] = array(1,$uid);
        $res = $this->db->save($tmpData,$options);
        if($res!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 重置用户密码操作
    public function resetUserPwd($uid,$pwd){
        $tmpData = array('user_password'=>'?');
        $options['table'] = 'user';
        $options['where'] = array('user_id'=>'?');
        $options['param'] = array(md5($pwd),$uid);
        $uid = $this->db->save($tmpData,$options);
        if($uid!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 用户修改自己的密码操作
    public function resetOwnPassword($param){
        $uid = $param['uid'];
        $old_pwd = $param['old_pwd'];
        $new_pwd = $param['new_pwd'];

        $options['table'] = 'admin';
        $options['where'] = array('admin_id'=>'?');
        $options['param'] = array($uid);
        $userOauth = $this->db->find($options);
        if(!empty($userOauth)){
            if($userOauth['password'] !== md5($old_pwd)){
                return $this->returnResult(402,array('Wrong Password'));
            }
        }else{
            return $this->returnResult(201,array('Account Not Exist'));
        }

        $tmpData = array('password'=>'?');
        $options1['table'] = 'admin';
        $options1['where'] = array('admin_id'=>'?');
        $options1['param'] = array(md5($new_pwd),$uid);
        $uid = $this->db->save($tmpData,$options1);
        if($uid !== FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    //添加用户
    public function addUser($param){
        $options['table'] = 'user';
        $options['where'] = array('user_name'=>'?');
        $options['param'] = array($param['uname']);
        $data = $this->db->find($options);
        if(empty($data)){
            $options1['table'] = 'user';
            $tmpData = array('user_name'=>'?','user_password'=>'?','register_time'=>'?','user_photo'=>'?');
            $options1['param'] = array($param['uname'],md5($param['password']),time(),$param['photo']);          
            $uid = $this->db->add($tmpData,$options1);

            if($uid!==FALSE){
                return $this->returnResult(200);
            }else {
                return $this->returnResult(4000);
            }
        }else{
            return $this->returnResult(201);            
        }
                
    }

    //趣事列表
    public function getThingsList($content,$count,$page){
        $options1['table']='user';
        $options1['where'] = array('is_delete'=>'?');
        $options1['param'] = array('0');
        $user = $this->db->select($options1);

        $options['table']='things';
        $options['where'] = array('is_delete'=>'?');
        $options['param'] = array('0');
        if(!empty($content)){
            $options['where'] = array_merge($options['where'] = array('things_content'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'] = array("%{$content}%"));
        }
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $options['limit']=($page-1)*$count.','.$count;
        $options['order']='publish_time DESC';
        $list = $this->db->select($options);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['publish_time'] = date('Y-m-d H:i:s',$v['publish_time']);
                foreach($user as $k1=>$v1){
                    if($user[$k1]['user_id'] === $list[$k]['user_id']){
                        $list[$k]['userInfo'] = $user[$k1];
                    }
                }
                // $list[$k]['privilege'] = $v['privilege']==0?'普通':($v['privilege']==2?'游戏商户':($v['privilege']>=6?'管理员':'其他'));
                // $list[$k]['state'] = $v['state']==0?'正常':'禁封';
            }
            $data = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    //审核趣事
    public function approvalThings($things_id){
        $options['table'] = 'things';
        $options['where'] = array('things_id'=>'?');
        $options['param'] = array($things_id);
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        
        $options1['table'] = 'things';
        $options1['where'] = array('things_id'=>'?');
        $tmpData = array('is_approval'=>'?');
        $options1['param'] = array(1,$things_id);
        
        $res = $this->db->save($tmpData,$options1);

        if($res!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    //删除趣事
    public function deleteThings($things_id){
        $options['table'] = 'things';
        $options['where'] = array('things_id'=>'?');
        $options['param'] = array($things_id);
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        
        $options1['table'] = 'things';
        $options1['where'] = array('things_id'=>'?');
        $tmpData = array('is_delete'=>'?');
        $options1['param'] = array(1,$things_id);
        $res = $this->db->save($tmpData,$options1);

        if($res!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    //评论列表
    public function getCommentList($content,$count,$page){
        $options1['table']='user';
        $options1['where'] = array('is_delete'=>'?');
        $options1['param'] = array('0');
        $user = $this->db->select($options1);
        // print_r($user);
        $options2['table']='things';
        $options2['where'] = array('is_delete'=>'?');
        $options2['param'] = array('0');
        $thing = $this->db->select($options2);
        // print_r($thing);

        $options['table']='comment';
        $options['where'] = array('is_delete'=>'?');
        $options['param'] = array('0');
        if(!empty($content)){
            $options['where'] = array_merge($options['where'] = array('content'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'] = array("%{$content}%"));
        }
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $options['limit']=($page-1)*$count.','.$count;
        $options['order']='comment_time DESC';
        $list = $this->db->select($options);
        // print_r($list);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['comment_time'] = date('Y-m-d H:i:s',$v['comment_time']);
                foreach($user as $k1=>$v1){
                    if($user[$k1]['user_id'] === $list[$k]['user_id']){
                        $list[$k]['userInfo'] = $user[$k1];
                    }
                }
                foreach($thing as $k2=>$v2){
                    if($thing[$k2]['things_id'] == $list[$k]['things_id']){
                        $list[$k]['thingInfo'] = $thing[$k2];
                    }
                }
            }
            $data = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    //审核评论
    public function approvalComment($comment_id){
        $options['table'] = 'comment';
        $options['where'] = array('comment_id'=>'?');
        $options['param'] = array($comment_id);
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        
        $options1['table'] = 'comment';
        $options1['where'] = array('comment_id'=>'?');
        $tmpData = array('is_approval'=>'?');
        $options1['param'] = array(1,$comment_id);
        
        $res = $this->db->save($tmpData,$options1);

        if($res!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    //删除评论
    public function deleteComment($comment_id){
        $options['table'] = 'comment';
        $options['where'] = array('comment_id'=>'?');
        $options['param'] = array($comment_id);
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        
        $options1['table'] = 'comment';
        $options1['where'] = array('comment_id'=>'?');
        $tmpData = array('is_delete'=>'?');
        $options1['param'] = array(1,$comment_id);
        $res = $this->db->save($tmpData,$options1);

        if($res!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    //管理员列表
    public function adminList(){
        $options['table']='admin';
        $options['field'] = 'admin_id,admin_name,cellphone,register_time';
        $list=$this->db->select($options);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['register_time'] = date('Y-m-d H:i:s',$v['register_time']);
            }
            return $this->returnResult(200,$list);
        }else{
            return $this->returnResult(201);
        }
    }

     //添加管理员账号
    public function addAdmin($oauthData){
        $options['table']='admin';
        $options['where']=array('admin_name'=>'?');
        $options['param']=array($oauthData['uname']);
        $result=$this->db->find($options);
        if(!empty($result)){
            return $this->returnResult(201);
        }
        $tmpData1=array('admin_name'=>'?','password'=>'?','register_time'=>'?','cellphone'=>'?');
        $options1['table']='admin';        
        $options1['param']=array($oauthData['uname'],md5($oauthData['password']),time(),$oauthData['cellphone']);
        $status=$this->db->add($tmpData1,$options1);
        if($status!==FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }

    //管理员编辑资料操作
    public function editAdmin($param){
        $options['table'] = 'admin';
        $options['where'] = array('admin_id'=>'?');
        $options['param'] = array($param['uid']);
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        if(empty($param['password'])){
            $tmpData = array('admin_name'=>'?','cellphone'=>'?');
            $options['param'] = array($param['uname'],$param['cellphone'],$param['uid']);
        }else{
            $tmpData = array('admin_name'=>'?','password'=>'?','cellphone'=>'?');
            $options['param'] = array($param['uname'],md5($param['password'],$param['cellphone']),$param['uid']);
        }
        
        $options['table'] = 'admin';
        $options['where'] = array('admin_id'=>'?');
        
        $uid = $this->db->save($tmpData,$options);

        if($uid!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    //删除管理员
    public function deleteAdmin($uid){
        $options['table'] = 'admin';
        $options['where'] = array('admin_id'=>'?');
        $options['param'] = array($uid);
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        
        $options1['table'] = 'admin';
        $options1['where'] = array('admin_id'=>'?');
        $options1['param'] = array($uid);
        $uid = $this->db->delete($options1);

        if($uid!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 管理员修改密码操作
    public function resetPwd($param){
        $options['table'] = 'admin';
        $options['where'] = array('admin_id'=>'?','password'=>'?');
        $options['param'] = array($param['uid'],md5($param['old_pwd']));
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        $tmpData = array('password'=>'?');
        $options1['table'] = 'user';
        $options1['where'] = array('uid'=>'?');
        $options1['param'] = array(md5($param['new_pwd']),$param['uid']);
        $uid = $this->db->save($tmpData,$options1);
        if($uid!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }
    
}

