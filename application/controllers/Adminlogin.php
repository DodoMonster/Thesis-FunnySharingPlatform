<?php
class AdminloginController extends \Core\BaseControllers {

    // public function init() {
    //     parent::init();
    //     if($this->_aid>0 && strtolower($this->_action)=='login'){
    //         //Header("Location:/admin/adminindex/home");
    //     }
    // }
    
    //管理员登录界面
    public function loginAction(){
        // echo "string";
        $this->display('login');
    }
    
    //登陆接口
    public function opLoginAction(){
        $loginData['account'] = isset($this->_postData['account']) ? $this->_postData['account'] : '';
        $loginData['password'] = isset($this->_postData['password']) ? $this->_postData['password'] : '';
        $model = new \Admin\AdminOauthModel();
        $data = $model->adminLogin($loginData);
         if($data['code'] == 200){
           $data['code'] = 0;
           $data['msg'] = '登陆成功！';
           $data['data'] = $data;
        }elseif ($data['code'] == 201) {
            $data['code'] = 1;
            $data['msg'] = '账号不存在！';
        }elseif ($data['code'] == 402) {
            $data['code'] = 1;
            $data['msg'] = '密码错误！';
        }else{
            $data['code'] = 1;
            $data['msg'] = '未知错误！';
        }
        echo json_encode($data);       
    }

    //生成图形验证码
    // public function getCaptchaAction(){
    //     $model = new \Addons\Captcha\CaptchaUtil('admin');
    //     $model->show();
    // }

    //退出登录
    // public function opLogoutAction(){
    //     $this->unsetOauthAdminSession();
    //     $this->redirect("/adminlogin/login");
    // }


}

