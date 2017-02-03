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
        echo "string";
        // $this->display('login');
    }
    
    //登陆接口
    public function opLoginAction(){
        $loginData['account'] = isset($this->_postData['account']) ? $this->_postData['account'] : '';
        $loginData['password'] = isset($this->_postData['password']) ? $this->_postData['password'] : '';
        $loginData['captcha'] = isset($this->_postData['captcha']) ? $this->_postData['captcha'] : '';
        $model = new \Admin\AdminOauthModel();
        $data = $model->adminLogin($loginData);
        if($data['code'] == 200){
            $this->setOauthAdminSession($data);
            $this->redirect("/newadmin/newadminindex/index");            
        }elseif ($data['code'] == 201) {
            echo "<script>alert('验证码错误');history.back();</script>";die;
        }elseif ($data['code'] == 401) {
             echo "<script>alert('账号或密码长度有误');history.back();</script>";die;
        }elseif ($data['code'] == 402) {
            echo "<script>alert('账号不存在或密码错误');history.back();</script>";die;
        }
    }

    //生成图形验证码
    public function getCaptchaAction(){
        $model = new \Addons\Captcha\CaptchaUtil('admin');
        $model->show();
    }

    //退出登录
    public function opLogoutAction(){
        $this->unsetOauthAdminSession();
        $this->redirect("/adminlogin/login");
    }
    

    public function testSphinxAction(){
        $s = new \Addons\Sphinx\SearchUtil([ 'snippet_fields' => ['title', 'content'], 'field_weights' => ['title' => 20, 'content' => 10], ]); 
        $s->setSortMode(SPH_SORT_EXTENDED, 'created desc,@weight desc'); 
        //$s->setSortBy('created desc,@weight desc'); 
        $words = $s->wordSplit("MySQL复制"); 
        $res = $s->query($words, 0, 10, 'master'); 
        var_dump($res);
    }

}

