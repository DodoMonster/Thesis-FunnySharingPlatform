<?php
class AdminoauthController extends \Core\BaseControllers {
    
    public function init() {
        parent::init();
        $this->getView()->assign('title', '后台管理系统');
        $this->getView()->assign('keywords', '');
        $this->getView()->assign('description', '');
        if($this->_aid>0 && strtolower($this->_action)=='login'){
            //Header("Location:/admin/adminindex/home");
        }
    }
    
    //管理员登录界面
    public function loginAction(){
        $this->getView()->assign('h1', '管理员登录');
        $this->getView()->assign('postUrl', '/admin/adminoauth/opLogin/');
        $this->display('admin-login');
    }
    
    //管理员登录
    public function opLoginAction(){
        $oauthData['account']=isset($this->_postData['account']) ? $this->_postData['account']: '';
        $oauthData['password']=isset($this->_postData['password']) ? $this->_postData['password']: '';
        $oauthData['captcha']=isset($this->_postData['captcha']) ? $this->_postData['captcha']: '';
        $model= new \Admin\AdminOauthModel();
        $data=$model->adminLogin($oauthData);
        if($data['code']==200){
            $this->setOauthAdminSession($data);
            $this->redirect("/admin/adminindex/home");
        }elseif($data['code']==201){
            echo "<script>alert('验证码错误');history.back();</script>";die;
        }elseif($data['code']==401){
            echo "<script>alert('账号或密码长度有误');history.back();</script>";die;
        }elseif($data['code']==402){
            echo "<script>alert('账号不存在或密码错误');history.back();</script>";die;
        }
    }
    
    //平台登出
    public function opLogoutAction(){
        $this->unsetOauthAdminSession();
        $this->redirect("/admin/adminoauth/login");
    }
    
    //图形验证码
    public function getCaptchaAction(){
        $model=new \Addons\Captcha\CaptchaUtil('admin'); 
        $model->show();
    }
    
    //
    public function testSphinxAction(){
        $s = new \Addons\Sphinx\SearchUtil([ 'snippet_fields' => ['title', 'content'], 'field_weights' => ['title' => 20, 'content' => 10], ]); 
        $s->setSortMode(SPH_SORT_EXTENDED, 'created desc,@weight desc'); 
        //$s->setSortBy('created desc,@weight desc'); 
        $words = $s->wordSplit("MySQL复制"); 
        $res = $s->query($words, 0, 10, 'master'); 
        var_dump($res);
    }
    
    public function dayReportListAction(){
        $gameInfo=[];
        $model = new \Admin\AdminIndexModel();     
        $gameList = $model->gameList([],$this->_count);
        $gameInfo['game_id'] = $gameList['data']['list'][2]['game_id'];
        $gameInfo['game_name'] = $gameList['data']['list'][2]['game_name'];
        $gameInfo['game_table_name'] = $gameList['data']['list'][2]['game_table_name'];
        $gameInfo['package_name'] = $gameList['data']['list'][2]['package_name'];
        $param['start']='';
        $param['channel_id']='';
        $param['end']='';
        $model1 = new \Admin\AdminIndexModel();
        $data=$model1->dayReportList($gameInfo,[],[],$param,$this->_count,$this->_page);
        //print_r($data);
        echo 'success';
    }
    
    public function testAction(){
        echo 'test';
    }
}

