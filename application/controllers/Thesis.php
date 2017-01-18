<?php
class thesisController extends \Core\BaseControllers {
    public function init() {
        parent::init();            
    }

    //判断用户是否登录
    public function checkIsLogin(){
        if($this->_uid <= 0){
            $data['code'] = -1;
            $data['msg'] = '请先登录';
            echo json_encode($data);exit;
        }  
    }

    //用户注册
    public function registerAction(){
        $oauthData['username']=isset($this->_postData['username']) ? $this->_postData['username']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        $model = new \Web\ThesisModel();
        $data = $model->register($oauthData);

        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '注册成功！';            
        }else{
            $data['code'] = 1;
            $data['msg'] = '注册失败，请重试'; 
        }
        echo json_encode($data);
    }

    //用户登录
    public function loginAction(){
        $oauthData['username']=isset($this->_postData['username']) ? $this->_postData['username']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        // print_r($oauthData);
        $model = new \Web\ThesisModel();
        $data = $model->login($oauthData);

        if($data['code'] == 200){             
            $this->setOauthAdminSession($data);
            // print_r($this->_uid);
            $data['code'] = 0;
            $data['data'] = $data;
            $data['msg'] = '登录成功！';            
        }elseif($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '账号不存在或密码错误！'; 
        }else{
            $data['code'] = 1;
            $data['msg'] = '登录失败，请重试'; 
        }
        echo json_encode($data);
    }
    
    //用户重置密码
    public function resetAction(){
        $oauthData['username']=isset($this->_postData['username']) ? $this->_postData['username']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        $model = new \Web\ThesisModel();
        $data = $model->reset($oauthData);

        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '重置密码成功！';            
        }elseif($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '账号不存在！'; 
        }else{
            $data['code'] = 1;
            $data['msg'] = '重置密码失败，请重试'; 
        }
        echo json_encode($data);
    }

    //发表趣事
    public function publishThingsAction(){    
        $this->checkIsLogin();
        $param['things_content']=isset($this->_postData['things_content']) ? $this->_postData['things_content']: '';
        if($_FILES && $_FILES['things_img']['tmp_name']){
            $tmp_name = $_FILES['things_img']['tmp_name'];
            $template = $_FILES['things_img']['name'];
            $res = move_uploaded_file($tmp_name, 'uploads/things_img/'.$template);//将上传的文件移动到新位置
            if(!$res){
                $data['code'] = 1;
                $data['msg'] = '图片上传失败，请重试！';
                echo json_encode($data);
            }else{
                $param['things_img'] = '/uploads/avatar/' . $template;
            }
        }else{
            $param['things_img'] = '';
        }
        $param['is_anonymous'] = isset($this->_postData['is_anonymous']) ? $this->_postData['is_anonymous']: '';
        
        // print_r($param);die;
        $model = new \Web\ThesisModel();
        $data = $model->publishThings($param,$this->_uid);

        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '发表趣事成功！';            
        }elseif($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '用户不存在！'; 
        }else{
            $data['code'] = 1;
            $data['msg'] = '发表趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //修改头像
    public function changeAvatarAction(){
        if($_FILES && $_FILES['photo']['tmp_name']){
            $tmp_name = $_FILES['photo']['tmp_name'];
            $photo = $_FILES['photo']['name'];
            $res = move_uploaded_file($tmp_name, 'uploads/avatar/' . $photo);//将上传的文件移动到新位置
            if(!$res){
                $data['code'] = 1;
                $data['msg'] = '图片上传失败，请重试！';
                echo json_encode($data);die;
            }else{
                $photo = '/uploads/avatar/' . $photo;
            }
        }else{
            $data['code'] = 1;
            $data['msg'] = '上传的头像不能为空！'; 
            echo json_encode($data);die;
        }
        $user_id = isset($this->_postData['user_id']) ? $this->_postData['user_id']: '';
        $model = new \Web\ThesisModel();
        $data = $model->changeAvatar($user_id,$photo);

        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '修改头像成功！';            
        }elseif($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '账号不存在！'; 
        }else{
            $data['code'] = 1;
            $data['msg'] = '修改头像失败，请重试'; 
        }
        echo json_encode($data);
    }

    //修改密码
    public function changePwdAction(){
        $oauthData['user_id']=isset($this->_postData['user_id']) ? $this->_postData['user_id']: $this->_uid;
        $oauthData['originPwd']=isset($this->_postData['originPwd']) ? $this->_postData['originPwd']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        $model = new \Web\ThesisModel();
        $data = $model->changePwd($oauthData);

        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '修改密码成功！';            
        }elseif($data['code'] == 201){
            $data['code'] = 1;
            $data['msg'] = '原始密码不正确！'; 
        }else{
            $data['code'] = 1;
            $data['msg'] = '修改密码失败，请重试'; 
        }
        echo json_encode($data);
    }

    //修改用户名
    public function changeUnameAction(){
        $oauthData['user_id'] = isset($this->_postData['user_id']) ? $this->_postData['user_id']: $this->_uid;
        $oauthData['uname']=isset($this->_postData['uname']) ? $this->_postData['uname']: '';
       
        $model = new \Web\ThesisModel();
        $data = $model->changeUname($oauthData);

        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '修改用户名成功！';            
        }else{
            $data['code'] = 1;
            $data['msg'] = '修改用户名失败，请重试'; 
        }
        echo json_encode($data);
    }
    //获取用户信息
    public function getUserInfoAction(){
        $uid = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getUserInfo($uid);

        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取用户信息成功！'; 
            $data['data'] = $data['data'];
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取用户信息失败，请重试'; 
        }
        echo json_encode($data);
    }

    //平台登出
    public function opLogoutAction(){
        $this->unsetOauthAdminSession();
        $data['code'] = 0;
        $data['msg'] = '退出成功！'; 
        echo json_encode($data);           
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