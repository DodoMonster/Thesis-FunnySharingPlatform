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
            $data['code'] = 0;
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
            $data['msg'] = '登录失败，请重试'; 
        }
        echo json_encode($data);
    }

    //发表趣事
    public function publishThingsAction(){    
        $this->checkIsLogin();
        $param['things_content']=isset($this->_postData['things_content']) ? $this->_postData['things_content']: '';
        $param['things_img']=isset($this->_postData['things_img']) ? $this->_postData['things_img']: '';
        $param['is_anonymous']=isset($this->_postData['is_anonymous']) ? $this->_postData['is_anonymous']: '';
        
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
    //平台登出
    public function opLogoutAction(){
        $this->unsetOauthAdminSession();
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