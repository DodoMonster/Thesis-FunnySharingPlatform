<?php

class AdminController extends \Core\BaseControllers{
    protected $_gameInfo=[];
    protected $_gameArray=[];
    protected $_adminInfo=[];


	// public function init() {
 //        parent::init();
 //        if($this->_aid <= 0){
 //            $this->error404Action();
 //        }
 //    }

    //页面不存在
    // public function error404Action(){
    //     Header("Location:/adminlogin/login");
    // }
    
    //首页
    public function indexAction(){
        $this->display('index');
    }

    //获取管理员信息
    public function getAdminInfoAction(){
        $model= new \Admin\AdminModel();
        $data = $model->getAdminInfo($this->_aid);

        if($data['code'] == 200){
            $data['code'] = 0;
            $data = $data['data'];
            echo json_encode($data);die;            
        }
        elseif($data['code'] == 201){
            $data['code'] = 0;
            $data['msg'] = '用户不存在';
            $data['data'] = array();
            echo json_encode($data);die;
        }
        else{
            $data['code'] = 1;
            $data['msg'] = '获取管理员信息失败，请重试！';
            echo json_encode($data);die;
        }        
    } 

    //获取管理员列表
    public function getAdminListAction(){
        $model= new \Admin\AdminModel();
        $data = $model->adminList();

        if($data['code'] == 200){
            $data = array('code'=>0,'msg'=>'请求成功','data'=>$data['data']);
            echo json_encode($data);die;            
        }
        elseif($data['code'] == 201){
            $data['code'] = 0;
            $data['msg'] = '列表为空';
            $data['data'] = array();
            echo json_encode($data);die;
        }
        else{
            $data['code'] = 1;
            $data['msg'] = '获取管理员列表失败，请重试！';
            echo json_encode($data);die;
        }        
    }    

    //添加管理员
    public function addAdminAction(){
        $oauthData['uname'] = isset($this->_postData['uname']) ? $this->_postData['uname']:'';
        $oauthData['password'] = isset($this->_postData['password']) ? $this->_postData['password']:'';
        $oauthData['cellphone'] = isset($this->_postData['cellphone']) ? $this->_postData['cellphone']:'';
        $model = new \Admin\AdminModel();
        $data = $model->addAdmin($oauthData);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '账号已存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "创建管理员成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '创建管理员失败，请重试！';        
        }
       
        echo json_encode($data);
    }

   //编辑管理员
    public function editAdminAction(){
        $oauthData['uid'] = isset($this->_postData['uid']) ? $this->_postData['uid']:'';
        $oauthData['uname'] = isset($this->_postData['uname']) ? $this->_postData['uname']:'';
        $oauthData['password'] = isset($this->_postData['password']) ? $this->_postData['password']:'';
        $oauthData['cellphone'] = isset($this->_postData['cellphone']) ? $this->_postData['cellphone']:'';

        $model = new \Admin\AdminModel();
        $data = $model->editAdmin($oauthData);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '管理员不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "编辑管理员资料成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '管理员资料编辑失败，请重试！';        
        }
       
        echo json_encode($data);
    }

    //删除管理员
    public function deleteAdminAction(){
        $uid = isset($this->_postData['uid']) ? $this->_postData['uid']:'';
        if(empty($uid)){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！';
            echo json_encode($data);die;            
        }
        $model = new \Admin\AdminModel();
        $data = $model->deleteAdmin($uid);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "删除管理员成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '删除管理员失败，请重试！';        
        }
       
        echo json_encode($data);
    }

    // 管理员修改密码操作
    public function adminResetPwdAction(){
        $param['uid'] = $this->_adminInfo['aid'];
        $param['old_pwd'] = isset($this->_postData['old_pwd']) ? trim($this->_postData['old_pwd']):'';
        $param['new_pwd'] = isset($this->_postData['new_pwd']) ? trim($this->_postData['new_pwd']):'';
        if(empty($param['uid'])){
            $data['code'] = 1;
            $data['msg'] = '不合法操作！';
            echo json_encode($data); die;
        }
        $model = new \Admin\AdminModel();
        $data = $model->resetPwd($param);
        if($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = '修改密码成功！';
        }elseif($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '原密码输入错误';
        }else{
            $data['code'] = 1;
            $data['msg'] = '密码修改失败，请重试！';
        }
        echo json_encode($data);
    }

    //获取用户列表
    public function getUserListAction(){
        $uname = isset($this->_getData['uname']) ? $this->_getData['uname']:'';
        $page = isset($this->_getData['page']) ? $this->_getData['page']:1;
        $model = new \Admin\AdminModel();
        $data = $model->userList($uname,$this->_count,$page);

        if($data['code'] == 200){
            $data = array('code'=>0,'msg'=>'请求成功','data'=>$data['data']);
            echo json_encode($data);die;            
        }
        elseif($data['code'] == 201){
            $data['code'] = 0;
            $data['msg'] = '用户列表为空';
            $data['data'] = array();
            echo json_encode($data);die;
        }
        else{
            $data['code'] = 1;
            $data['msg'] = '获取用户列表失败，请重试！';
            echo json_encode($data);die;
        }        
    }    

    //删除用户
    public function deleteUserAction(){
        $uid = isset($this->_postData['uid']) ? $this->_postData['uid']:'';
        $model = new \Admin\AdminModel();
        $data = $model->deleteUser($uid);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '账号不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "删除用户成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '删除用户失败，请重试！';        
        }
       
        echo json_encode($data);
    }

    //重置用户密码
    public function resetUserPwdAction(){
        $uid = isset($this->_postData['uid']) ? $this->_postData['uid']:'';
        $password = isset($this->_postData['password']) ? $this->_postData['password']:'';        
        $model = new \Admin\AdminModel();
        $data = $model->resetUserPwd($uid,$password);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '账号不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "重置用户密码成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '重置用户密码失败，请重试！';        
        }
       
        echo json_encode($data);
    }

    //添加用户
    public function addUserAction(){
        $oauthData['uname'] = isset($this->_postData['uname']) ? $this->_postData['uname']:'';
        $oauthData['password'] = isset($this->_postData['password']) ? $this->_postData['password']:'';
        $oauthData['photo'] = '/uploads/avatar/default-avatar.png';
        $model = new \Admin\AdminModel();
        $data = $model->addUser($oauthData);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户已存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "创建用户成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '创建用户失败，请重试！';        
        }
       
        echo json_encode($data);
    }

   //编辑用户状态
    public function editUserAction(){
        $oauthData['uid'] = isset($this->_postData['uid']) ? $this->_postData['uid']:'';
        $oauthData['uname'] = isset($this->_postData['uname']) ? $this->_postData['uname']:'';
        $oauthData['password'] = isset($this->_postData['password']) ? $this->_postData['password']:'';
        $oauthData['cellphone'] = isset($this->_postData['cellphone']) ? $this->_postData['cellphone']:'';

        $model = new \Admin\AdminModel();
        $data = $model->editUser($oauthData);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "编辑用户资料成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '用户资料编辑失败，请重试！';        
        }
       
        echo json_encode($data);
    }

    //获取趣事列表
    public function getThingsListAction(){
        $content = isset($this->_getData['content']) ? $this->_getData['content']:'';
        $page = isset($this->_getData['page']) ? $this->_getData['page']:1;
        $model = new \Admin\AdminModel();
        $data = $model->getThingsList($content,$this->_count,$page);
        if($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = '请求趣事列表';                        
            $data['data'] = $data['data'];
            echo json_encode($data);die;            
        }
        elseif($data['code'] == 201){
            $data['code'] = 0;
            $data['msg'] = '趣事列表为空';
            $data['data']['list'] = array();
            echo json_encode($data);die;
        }
        else{
            $data['code'] = 1;
            $data['msg'] = '获取趣事列表失败，请重试！';
            echo json_encode($data);die;
        }        
    }  

     //删除趣事
    public function deleteThingsAction(){
        $things_id = isset($this->_postData['things_id']) ? $this->_postData['things_id']:'';
        $model = new \Admin\AdminModel();
        $data = $model->deleteThings($things_id);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "删除趣事成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '删除趣事失败，请重试！';        
        }
       
        echo json_encode($data);
    }

     //审核趣事
    public function approvalThingsAction(){
        $things_id = isset($this->_postData['things_id']) ? $this->_postData['things_id']:'';
        $model = new \Admin\AdminModel();
        $data = $model->approvalThings($things_id);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "审核趣事成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '审核趣事失败，请重试！';        
        }
       
        echo json_encode($data);
    }  

    //获取评论列表
    public function getCommentListAction(){
        $content = isset($this->_getData['content']) ? $this->_getData['content']:'';
        $page = isset($this->_getData['page']) ? $this->_getData['page']:1;
        $model = new \Admin\AdminModel();
        $data = $model->getCommentList($content,$this->_count,$page);
        if($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = '请求评论列表';                        
            $data['data'] = $data['data'];
            echo json_encode($data);die;            
        }
        elseif($data['code'] == 201){
            $data['code'] = 0;
            $data['msg'] = '评论列表为空';
            $data['data']['list'] = array();
            echo json_encode($data);die;
        }
        else{
            $data['code'] = 1;
            $data['msg'] = '获取评论列表失败，请重试！';
            echo json_encode($data);die;
        }        
    }  

     //删除评论
    public function deleteCommentAction(){
        $comment_id = isset($this->_postData['comment_id']) ? $this->_postData['comment_id']:'';
        $model = new \Admin\AdminModel();
        $data = $model->deleteComment($comment_id);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "删除评论成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '删除评论失败，请重试！';        
        }
       
        echo json_encode($data);
    }

     //审核评论
    public function approvalCommentAction(){
        $comment_id = isset($this->_postData['Comment_id']) ? $this->_postData['Comment_id']:'';
        $model = new \Admin\AdminModel();
        $data = $model->approvalComment($comment_id);

        if($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！';
        }elseif($data['code'] == 200){
            $data['code'] = 0;
            $data['msg'] = "审核评论成功！";
        }else {
            $data['code'] = 1;
            $data['msg'] = '审核评论失败，请重试！';        
        }
       
        echo json_encode($data);
    }  
}