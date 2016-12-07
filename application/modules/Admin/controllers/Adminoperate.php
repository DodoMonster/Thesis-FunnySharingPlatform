<?php
class AdminoperateController extends \Core\BaseControllers {
    
    public function init() {
        parent::init();
        if($this->_aid<=0){
             $this->error404Action();
        }
        $this->_gameInfo=$_SESSION['game'];
    }
    
    //页面不存在
    public function error404Action(){
        echo '页面不存在';
        Header("Location:/admin/adminoauth/login");
    }
    
    // 游戏创建操作;
    public function opGameAddAction(){
        $param['game_name'] = isset($this->_postData['game_name'])?$this->_postData['game_name']:'';
        $param['table_name'] = isset($this->_postData['table_name'])?$this->_postData['table_name']:'';
        $param['package_name'] = isset($this->_postData['package_name'])?$this->_postData['package_name']:'';
        $param['redirect_uri'] = isset($this->_postData['redirect_uri'])?$this->_postData['redirect_uri']:'';
        $param['home_uri'] = isset($this->_postData['home_uri'])?$this->_postData['home_uri']:'';
        $param['bbs_uri'] = isset($this->_postData['bbs_uri'])?$this->_postData['bbs_uri']:'';
        $param['remark'] = isset($this->_postData['remark'])?$this->_postData['remark']:'';
        //TODO
        if(!$param['game_name'] || !$param['table_name']){
            echo "<script>alert('请填写带*的必填项');history.back();</script>";
        }       
        $model = new \Admin\AdminIndexModel();
        $data = $model->opGameAdd($param);
        if($data['code']==200){
             echo "<script>window.location.href='/admin/adminindex/gamelist';</script>";
        }elseif($data['code']==201){
            echo "<script>alert('游戏名称、表名已经存在');history.back();</script>";
        }else{
            echo "<script>alert('游戏添加失败');history.back();</script>";
        }       
    }

    // 游戏编辑操作
    public function opGameEditAction(){
        $param['game_id'] = trim($this->_postData['game_id']);
        $param['game_name'] = trim($this->_postData['game_name']);
        $param['table_name'] = trim($this->_postData['table_name']);
        $param['package_name'] = trim($this->_postData['package_name']);
        $param['redirect_uri'] = trim($this->_postData['redirect_uri']);
        $param['home_uri'] = trim($this->_postData['home_uri']);
        $param['bbs_uri'] = trim($this->_postData['bbs_uri']);
        $param['remark'] = trim($this->_postData['remark']);
        //print_r($param);exit;
        if(!$param['game_id']){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        if(!$param['game_name'] || !$param['table_name'] || !$param['package_name']){
            echo "<script>alert('请填写带*的必填项');history.back();</script>";die;
        }
        $model = new \Admin\AdminIndexModel();
        $data = $model->opGameEdit($param);
        if($data['code']==201){
            echo "<script>alert('游戏名称已经存在');history.back();</script>";die;
        }
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('游戏编辑失败');history.back();</script>";
        }
    }

    // 用户编辑操作
    public function opUserEditAction(){
        $param['uid'] = isset($this->_postData['uid']) ? trim($this->_postData['uid']):'';
        $param['state'] = isset($this->_postData['state']) ? trim($this->_postData['state']):'';
        //print_r($param) ;exit;
        $model = new \Admin\AdminIndexModel();
        $data = $model->opUserEdit($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('用户编辑失败');history.back();</script>";
        }
    }

    // 普通用户重置密码操作
    public function opUserResetPwdAction(){
        $param['uid'] = isset($this->_postData['uid']) ? trim($this->_postData['uid']):'';
        $param['pwd'] = isset($this->_postData['pwd']) ? trim($this->_postData['pwd']):'';
        //print_r($param);exit;
        $model = new \Admin\AdminIndexModel();
        $data = $model->opUserResetPwd($param);
        if($data['code']==201){
            echo "<script>alert('该用户不存在');history.back();</script>";die;
        }
        if($data['code']==202){
            echo "<script>alert('该用户不是普通用户');history.back();</script>";die;
        }
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else{
            echo "<script>alert('密码重置失败');history.back();</script>";
        }
    }

    // 管理员修改密码操作
    public function opAdminResetPwdAction(){
        $param['uid'] = isset($this->_postData['uid']) ? $this->_postData['uid']:'';
        $param['old_pwd'] = isset($this->_postData['old_pwd']) ? trim($this->_postData['old_pwd']):'';
        $param['new_pwd'] = isset($this->_postData['new_pwd']) ? trim($this->_postData['new_pwd']):'';
        $param['confirm_pwd'] = isset($this->_postData['confirm_pwd']) ? trim($this->_postData['confirm_pwd']):'';
        if(empty($param['uid'])){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        if(empty($param['old_pwd']) || empty($param['new_pwd']) || empty($param['confirm_pwd'])){
            echo "<script>alert('请填写带*的必填项');history.back();</script>";die;
        }
        if($param['new_pwd']!==$param['confirm_pwd']){
            echo "<script>alert('新密码与确认密码不一致');history.back();</script>";die;
        }
        $model = new \Admin\AdminIndexModel();
        $data = $model->opAdminResetPwd($param);
        if($data['code']==201){
            echo "<script>alert('原密码输入错误');history.back();</script>";die;
        }
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else{
            echo "<script>alert('密码修改失败');history.back();</script>";
        }
    }

    // 渠道添加操作
    public function opChannelAddAction(){
        $param['game_id'] = isset($this->_postData['game_id'])?$this->_postData['game_id']:'';
        $param['channel_name'] = isset($this->_postData['channel_name'])?$this->_postData['channel_name']:'';
        $param['responsible_man'] = isset($this->_postData['responsible_man'])?$this->_postData['responsible_man']:'';
        $param['channel_package_name'] = isset($this->_postData['channel_package_name'])?$this->_postData['channel_package_name']:'';
        $param['download_url'] = isset($this->_postData['download_url'])?$this->_postData['download_url']:'';
        //print_r($param);exit;
        $model = new \Admin\AdminIndexModel();
        $data = $model->opChannelAdd($param);
        if($data['code']==200){
            echo "<script>window.location.href='/admin/adminindex/channellist';</script>";
        }elseif($data['code']==201){
            echo "<script>alert('该游戏渠道名称已存在');history.back();</script>";
        }else{
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }

    // 渠道编辑操作
    public function opChannelEditAction(){
        $param = array();
        $param['channel_id'] = $this->_postData['channel_id'];
        $param['channel_name'] = trim($this->_postData['channel_name']);
        $param['game_id'] = $this->_postData['game'];
        $param['responsible_man'] = trim($this->_postData['responsible_man']);
        $param['channel_package_name'] = trim($this->_postData['channel_package_name']);
        $param['download_url'] = trim($this->_postData['download_url']);
        //print_r($param);exit;
        if(!$param['channel_id']){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        $model = new \Admin\AdminIndexModel();
        $data = $model->opChannelEdit($param);
        if($data['code']==201){
            echo "<script>alert('该游戏渠道名称、渠道包名或下载地址已存在');history.back();</script>";die;
        }
        if($data['code']==200){
            echo "<script>window.location.href='/admin/adminindex/channellist';</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    //补单操作
    public function opNotifyGameServerAction(){
        $param['trade_no'] = isset($this->_postData['trade_no']) ? trim($this->_postData['trade_no']):'';
        $model = new \Admin\AdminIndexModel();
        $data = $model->opNotifyGameServer($this->_gameInfo,$param);
        //$this->returnValue($data);
        if($data['code']==200){
            echo "<script>alert('补单成功');window.location.href='/admin/adminindex/paylist';</script>";die;
        }else {
            echo "<script>alert('补单失败');history.back();</script>";
        }
    }

    // 支付记录详情
    public function payDetailAction(){
        $payType = isset($this->_postData['pay_type']) ? $this->_postData['pay_type']:'';
        $tradeNo = isset($this->_postData['trade_no']) ? $this->_postData['trade_no']:'';
        $table = $payType=='阿里支付' ? 'log_user_pay_ali':($payType=='微信支付' ? 'log_user_pay_wechat':($payType=='易宝支付' ? 'log_user_pay_yee':''));
        $model= new \Admin\AdminIndexModel();
        $data = $model->payDetail($table,$tradeNo);
        $data['data']['pay_type'] = $payType;
        $this->returnValue($data);
    }

    // 管理员编辑操作
    public function opadmineditAction(){
        $param['uid'] = isset($this->_postData['uid']) ? trim($this->_postData['uid']):'';
        $param['state'] = isset($this->_postData['state']) ? trim($this->_postData['state']):'';
        $param['role'] = isset($this->_postData['role']) ? trim($this->_postData['role']):'';
        //print_r($param);exit;
        if(empty($param['uid'])){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        $model = new \Admin\AdminOperateModel();
        $data = $model->opAdminEdit($param);
        if($data['code']==201){
            echo "<script>alert('超级管理员不可编辑');history.back();</script>";die;
        }
        if($data['code']==200){
            echo "<script>window.location.href='/admin/adminindex/adminlist';</script>";die;
        }else {
            echo "<script>alert('用户编辑失败');history.back();</script>";
        }
    }
    
    //添加管理员账号
    public function opAddAdminAction(){
        $oauthData['uname'] = isset($this->_postData['uname']) ? $this->_postData['uname']:'';
        $oauthData['password'] = isset($this->_postData['password']) ? $this->_postData['password']:'';
        $oauthData['role_id'] = isset($this->_postData['role_id']) ? $this->_postData['role_id']:'';
        $oauthData['ip'] = $this->_remoteIp;
        $model=new \Admin\AdminOperateModel();
        $data=$model->opAddAdmin($oauthData);
        //$this->returnValue($data);
        if($data['code']==201){
            echo "<script>alert('账号已存在');history.back();</script>";die;
        }
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }
    
    //添加管理员角色账号
    public function opAddAdminRoleAction(){
        $oauthData['role_name'] = isset($this->_postData['role_name']) ? $this->_postData['role_name']:'';
        $oauthData['menus'] = isset($this->_postData['menus']) ? $this->_postData['menus']:'';
        $oauthData['products'] = isset($this->_postData['products']) ? $this->_postData['products']:'';
        $oauthData['channels'] = isset($this->_postData['channels']) ? $this->_postData['channels']:'';
        $oauthData['columns'] = isset($this->_postData['columns']) ? $this->_postData['columns']:'';
        //print_r($oauthData);exit;
        if(!$oauthData['menus'] || !$oauthData['products'] || !$oauthData['channels'] || !$oauthData['columns']){
            echo "<script>alert('权限组不能全为空');history.back();</script>";die;
        }
        foreach($oauthData as $k=>$v){
            if(!empty($v) && is_array($v)){
                $oauthData[$k] = implode(',',$v);
            }
        }
        $model=new \Admin\AdminOperateModel();
        $data=$model->opAddAdminRole($oauthData);
        if($data['code']==201){
            echo "<script>alert('角色已存在');history.back();</script>";die;
        }
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }

    //编辑管理员角色账号
    public function opEditAdminRoleAction(){
        $oauthData['role_id'] = isset($this->_postData['role_id']) ? $this->_postData['role_id']:'';
        $oauthData['role_name'] = isset($this->_postData['role_name']) ? $this->_postData['role_name']:'';
        $oauthData['menus'] = isset($this->_postData['menus']) ? $this->_postData['menus']:'';
        $oauthData['products'] = isset($this->_postData['products']) ? $this->_postData['products']:'';
        $oauthData['channels'] = isset($this->_postData['channels']) ? $this->_postData['channels']:'';
        $oauthData['columns'] = isset($this->_postData['columns']) ? $this->_postData['columns']:'';
        //print_r($oauthData);exit;
        if(!$oauthData['role_id']){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        if(!$oauthData['menus'] || !$oauthData['products'] || !$oauthData['channels'] || !$oauthData['columns']){
            echo "<script>alert('权限组不能全为空');history.back();</script>";die;
        }
        foreach($oauthData as $k=>$v){
            if(!empty($v) && is_array($v)){
                $oauthData[$k] = implode(',',$v);
            }
        }
        //print_r($oauthData);exit;
        $model=new \Admin\AdminOperateModel();
        $data=$model->opEditAdminRole($oauthData);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    // 发布文章操作
    public function opAddArticleAction(){
        $param['title'] = isset($this->_postData['title'])? trim($this->_postData['title']):'';
        $param['category_id'] = isset($this->_postData['category'])? $this->_postData['category']:'';
        $param['add_time'] = isset($this->_postData['add_time'])? $this->_postData['add_time']:'';
        $param['is_recommend'] = isset($this->_postData['recommend'])? $this->_postData['recommend']:'';
        $param['content'] = isset($this->_postData['content'])? $this->_postData['content']:'';
        $param['game_id'] = isset($this->_postData['extra_game_id'])? $this->_postData['extra_game_id']:'';

        $str = preg_replace('/\//','\/',REAL_RESOURCE_URL);
        $param['content'] = htmlspecialchars(preg_replace('/src="'.$str.'/','src="',$param['content']));      // 图片存入数据库去掉源域名
        //print_r($param);exit;
        if(empty($param['game_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        if(empty($param['content'])){
            echo "<script>alert('内容不能为空');history.back();</script>";die;
        }
        $model = new \Admin\AdminIndexModel();
        $data = $model->opAddArticle($param);
        if($data['code']==200){
            echo "<script>window.location.href='/admin/adminindex/articlelist';</script>";die;
        }else {
            echo "<script>alert('发布失败');history.back();</script>";
        }
    }

    // 文章内容转码
    public function articleContentDecodeAction(){
        $content = isset($this->_postData['content'])?$this->_postData['content']:'';
        if(!empty($content)){
            $data =  array('code'=>200,'message'=>'success','data'=>htmlspecialchars_decode($content));
        }else {
            $data = array('code'=>201,'fail','data'=>'none');
        }
        return $this->returnValue($data);
    }

    // 文章编辑操作
    public function opEditArticleAction(){
        $param['article_id'] = isset($this->_postData['aid'])?$this->_postData['aid']:'';
        $param['title'] = isset($this->_postData['title'])? trim($this->_postData['title']):'';
        $param['category_id'] = isset($this->_postData['category'])? $this->_postData['category']:'';
        $param['add_time'] = isset($this->_postData['add_time'])? $this->_postData['add_time']:'';
        $param['is_recommend'] = isset($this->_postData['recommend'])? $this->_postData['recommend']:'';
        $param['content'] = isset($this->_postData['content'])? $this->_postData['content']:'';
        $param['visit_times'] = isset($this->_postData['visit_times'])? $this->_postData['visit_times']:'';
        if(!empty($param['visit_times']) && (!is_numeric($param['visit_times']) || bccomp($param['visit_times'],0)==-1)){
            echo "<script>alert('阅读次数为正整数');history.back();</script>";die;
        }

        $resourceStr = preg_replace('/\//','\/',REAL_RESOURCE_URL);
        $param['content'] = preg_replace('/src="'.$resourceStr.'/','src="',$param['content']);                      // 去掉源域名
        $cdnStr = preg_replace('/\//','\/',CDN_URL);
        $param['content'] = htmlspecialchars(preg_replace('/src="'.$cdnStr.'/','src="',$param['content']));         // 去掉CDN域名

        $model = new \Admin\AdminIndexModel();
        $data = $model->opEditArticle($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    // 文章删除操作
    public function opDeleteArticleAction(){
        $param['article_id'] = isset($this->_getData['article_id'])?$this->_getData['article_id']:'';
        if(empty($param['article_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opDeleteArticle($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('删除失败');history.back();</script>";
        }
    }

    // 幻灯片添加操作
    public function opAddSlideAction(){
        $param['url'] = isset($this->_postData['url'])?trim($this->_postData['url']):'';
        $param['is_mobile'] = isset($this->_postData['is_mobile'])?$this->_postData['is_mobile']:'';
        $param['sort'] = isset($this->_postData['sort'])?$this->_postData['sort']:'';
        $param['game_id'] = isset($this->_postData['extra_game_id'])? $this->_postData['extra_game_id']:'';
        if(empty($param['game_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        if(!empty($param['sort']) && !is_numeric($param['sort'])){
            echo "<script>alert('排序为数字');history.back();</script>";die;
        }
        if(empty($_FILES['pic']['name'][0])){
            echo "<script>alert('请选择上传图片');history.back();</script>";die;
        }
        $model=new Addons\Images\Images();
        $data=$model->uploadFileByUcloud('pic');
        if($data['code']==200){
            $param['img'] = $data['data']['images'][0];
        }else {
            echo "<script>alert('图片上传失败');history.back();</script>";die;
        }
        //print_r($param);exit;
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opAddSlide($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('发布失败');history.back();</script>";
        }
    }

    // 幻灯片编辑操作
    public function opEditSlideAction(){
        $param['slide_url'] = isset($this->_postData['url'])?trim($this->_postData['url']):'';
        $param['is_mobile'] = isset($this->_postData['is_mobile'])?trim($this->_postData['is_mobile']):'';
        $param['sort'] = isset($this->_postData['sort'])?$this->_postData['sort']:'';
        $param['game_id'] = isset($this->_postData['game'])?$this->_postData['game']:'';
        $param['slide_id'] = isset($this->_postData['sid'])?$this->_postData['sid']:'';
        $param['slide_img'] = '';
        if(empty($param['slide_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        if(!empty($param['sort']) && !is_numeric($param['sort'])){
            echo "<script>alert('排序为数字');history.back();</script>";die;
        }
        if($_FILES['pic']['name'][0]) {
            $model=new Addons\Images\Images();
            $data=$model->uploadFileByUcloud('pic');
            if($data['code']==200){
                $param['slide_img'] = $data['data']['images'][0];
            }else {
                echo "<script>alert('图片上传失败');history.back();</script>";die;
            }
        }
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opEditSlide($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    // 删除幻灯片操作
    public function opDeleteSlideAction(){
        $param['slide_id'] = isset($this->_getData['slide_id'])?$this->_getData['slide_id']:'';
        if(empty($param['slide_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opDeleteSlide($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('删除失败');history.back();</script>";
        }
    }

    // 礼包添加操作
    public function opAddGiftAction(){
        $param['gift_name'] = isset($this->_postData['gift_name'])?trim($this->_postData['gift_name']):'';
        $param['content'] = isset($this->_postData['content'])?$this->_postData['content']:'';
        $param['instruction'] = isset($this->_postData['instruction'])?$this->_postData['instruction']:'';
        $param['gift_limit'] = isset($this->_postData['gift_limit'])?$this->_postData['gift_limit']:'';
        $param['game_id'] = isset($this->_postData['extra_game_id'])? $this->_postData['extra_game_id']:'';
        if(empty($param['game_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        if(!empty($param['gift_limit']) && !is_numeric($param['gift_limit'])){
            echo "<script>alert('领取次数为数字');history.back();</script>";die;
        }
        $model=new Addons\Images\Images();
        $data=$model->uploadFileByUcloud('pic');
        if($data['code']==200){
            $param['gift_img'] = $data['data']['images'][0];
        }else {
            echo "<script>alert('图片上传失败');history.back();</script>";die;
        }
        //print_r($param);exit;
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opAddGift($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('发布失败');history.back();</script>";
        }
    }

    // 礼包编辑操作
    public function opeditgiftAction(){
        $param['gift_name'] = isset($this->_postData['gift_name'])?trim($this->_postData['gift_name']):'';
        $param['content'] = isset($this->_postData['content'])?trim($this->_postData['content']):'';
        $param['instruction'] = isset($this->_postData['instruction'])?$this->_postData['instruction']:'';
        $param['gift_limit'] = isset($this->_postData['gift_limit'])?$this->_postData['gift_limit']:'';
        $param['gift_id'] = isset($this->_postData['gift_id'])?$this->_postData['gift_id']:'';
        $param['gift_img'] = '';
        if(empty($param['gift_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        if(!empty($param['gift_limit']) && !is_numeric($param['gift_limit'])){
            echo "<script>alert('领取次数为数字');history.back();</script>";die;
        }
        if($_FILES['pic']['name'][0]) {
            $model=new Addons\Images\Images();
            $data=$model->uploadFileByUcloud('pic');
            if($data['code']==200){
                $param['gift_img'] = $data['data']['images'][0];
            }else {
                echo "<script>alert('图片上传失败');history.back();</script>";die;
            }
        }
        //print_r($param);exit;
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opEditGift($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    // 删除礼包操作
    public function opDeleteGiftAction(){
        $param['gift_id'] = isset($this->_getData['gift_id'])?$this->_getData['gift_id']:'';
        if(empty($param['gift_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opDeleteGift($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('删除失败');history.back();</script>";
        }
    }

    // 添加兑换码操作
    public function opAddExchangeCodeAction(){
        $param['gift_id'] = isset($this->_postData['gift_id'])?$this->_postData['gift_id']:'';
        if(empty($param['gift_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        //var_dump($_FILES);exit;
        $suffix = pathinfo($_FILES['code']['name'], PATHINFO_EXTENSION);
        if(!in_array($suffix,array('txt','csv'))){
            echo "<script>alert('请选择txt或csv文件');history.back();</script>";die;
        }
        $param['tmp_name'] = $_FILES['code']['tmp_name'];
        $model = new \Admin\AdminIndexModel();
        $data = $model->opAddExchangeCode($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }

    // 添加素材操作
    public function opAddMaterialAction(){
        $category = isset($this->_postData['category'])?$this->_postData['category']:'';
        $param['game_id'] = isset($this->_postData['extra_game_id'])?$this->_postData['extra_game_id']:'';
        $param['material_url'] = '';
        $param['outside_url'] = '';
        if(empty($param['game_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";die;
        }
        $model=new Addons\Images\Images();
        switch($category){
            case 1:
                if(empty($_FILES['orig_picture']['name'][0])){
                    echo "<script>alert('请选择原图');history.back();</script>";die;
                }
                $data=$model->uploadFileByUcloud('orig_picture');
                $param['category'] = 'origPicture';
                if($data['code']==200){
                    $param['material_url'] = $data['data']['images'][0];
                }else{
                    echo "<script>alert('上传原图失败');history.back();</script>";die;
                }
                break;
            case 2:
                if(empty($_FILES['screen_shot']['name'][0])){
                    echo "<script>alert('请选择截图');history.back();</script>";die;
                }
                $data=$model->uploadFileByUcloud('screen_shot');
                $param['category'] = 'screenShot';
                if($data['code']==200){
                    $param['material_url'] = $data['data']['images'][0];
                }else {
                    echo "<script>alert('上传截图失败');history.back();</script>";die;
                }
                break;
            case 3:
                $param['outside_url'] = isset($this->_postData['outside_url'])?trim($this->_postData['outside_url']):'';
                if(empty($param['outside_url'])){
                    echo "<script>alert('请填写外部链接');history.back();</script>";die;
                }
                $param['category'] = 'video';
                break;
        }
        $thumbData=$model->uploadFileByUcloud('thumb_img');
        if($thumbData['code']==200){
            $param['thumb_img'] = $thumbData['data']['images'][0];
        }else {
            echo "<script>alert('上传缩略图失败');history.back();</script>";die;
        }
        //print_r($param);exit;
        $model = new \Admin\AdminIndexModel();
        $data = $model->opAddMaterial($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('发布失败');history.back();</script>";die;
        }
    }

    // 删除素材操作
    public function opDeleteMaterialAction(){
        $param['material_id'] = isset($this->_getData['material_id'])?$this->_getData['material_id']:'';
        if(empty($param['material_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        $model1 = new \Admin\AdminIndexModel();
        $data = $model1->opDeleteMaterial($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('删除失败');history.back();</script>";
        }
    }

    // 发布天豫官网文章操作
    public function opAddTyArticleAction(){
        $param['title'] = isset($this->_postData['title'])? trim($this->_postData['title']):'';
        $param['add_time'] = isset($this->_postData['add_time'])? $this->_postData['add_time']:'';
        $param['is_recommend'] = isset($this->_postData['recommend'])? $this->_postData['recommend']:'';
        $param['content'] = isset($this->_postData['content'])? $this->_postData['content']:'';

        $str = preg_replace('/\//','\/',REAL_RESOURCE_URL);
        $param['content'] = htmlspecialchars(preg_replace('/src="'.$str.'/','src="',$param['content']));      // 图片存入数据库去掉源域名
        //print_r($param);exit;
        if(empty($param['content'])){
            echo "<script>alert('内容不能为空');history.back();</script>";die;
        }
        //print_r($param);exit;
        $model=new Addons\Images\Images();
        $data=$model->uploadFileByUcloud('pic');
        if($data['code']==200){
            $param['article_img'] = $data['data']['images'][0];
        }else{
            echo "<script>alert('上传原图失败');history.back();</script>";die;
        }
        //print_r($param);exit;
        $model1 = new \Admin\AdminIndexModel();
        $data1 = $model1->opAddTyArticle($param);
        if($data1['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('发布失败');history.back();</script>";
        }
    }

    // 编辑天豫官网文章操作
    public function opEditTyArticleAction(){
        $param['article_id'] = isset($this->_postData['aid'])?$this->_postData['aid']:'';
        $param['title'] = isset($this->_postData['title'])? trim($this->_postData['title']):'';
        $param['add_time'] = isset($this->_postData['add_time'])? $this->_postData['add_time']:'';
        $param['is_recommend'] = isset($this->_postData['recommend'])? $this->_postData['recommend']:'';
        $param['content'] = isset($this->_postData['content'])? $this->_postData['content']:'';
        $resourceStr = preg_replace('/\//','\/',REAL_RESOURCE_URL);
        $param['content'] = preg_replace('/src="'.$resourceStr.'/','src="',$param['content']);                      // 去掉源域名
        $cdnStr = preg_replace('/\//','\/',CDN_URL);
        $param['content'] = htmlspecialchars(preg_replace('/src="'.$cdnStr.'/','src="',$param['content']));         // 去掉CDN域名
        $param['article_img'] = '';
        if($_FILES['pic']['name'][0]) {
            $model=new Addons\Images\Images();
            $data=$model->uploadFileByUcloud('pic');
            if($data['code']==200){
                $param['article_img'] = $data['data']['images'][0];
            }else {
                echo "<script>alert('图片上传失败');history.back();</script>";die;
            }
        }
        //print_r($param);exit;
        $model = new \Admin\AdminIndexModel();
        $data = $model->opEditTyArticle($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    // 删除礼包操作
    public function opDeleteTyArticleAction(){
        $param['article_id'] = isset($this->_getData['article_id'])?$this->_getData['article_id']:'';
        if(empty($param['article_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        $model = new \Admin\AdminIndexModel();
        $data = $model->opDeleteTyArticle($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('删除失败');history.back();</script>";
        }
    }

    // 编辑游戏包操作
    public function opEditPackageAction(){
        $param['game_id'] = isset($this->_postData['game_id'])?$this->_postData['game_id']:'';
        $param['ios_package'] = isset($this->_postData['ios_package'])?$this->_postData['ios_package']:'';
        $param['android_package'] = isset($this->_postData['android_package'])?$this->_postData['android_package']:'';
        $param['weiduan_package'] = isset($this->_postData['weiduan_package'])?$this->_postData['weiduan_package']:'';
        //print_r($param);exit;
        if(empty($param['game_id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        $model = new \Admin\AdminIndexModel();
        $data = $model->opeditpackage($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    // 上传游戏包操作
    public function opUploadPackageAction(){
        $model=new Addons\Images\Images();
        $data=$model->uploadFile('file');
        //print_r($data);exit;
        if($data['code']==200){
            $data = CDN_URL.$data['data']['file'];
        }
        print_r($data);
    }

    // 插入小说文章数据
    public function opAddNoveDataAction(){
        $time =  strtotime('2016-06-20 16:32:59');
        $path = 'data/nove.txt';
        $file = fopen($path,'r');
        $i = 0;
        $j = 0;
        $arr = array();
        $content = '';
        while(!feof($file)){
            $row =  fgets($file);
            if(strpos($row,"title")!==false){
                if($j>=1){      // 第二个标题开始进入下一篇文章，清空content
                    $content = '';
                    $i++;
                }
                if(($i%200==0 || feof($file)) && $i!=0){
                    //print_r($arr);
                    $str = '';
                    foreach($arr as $k=>$v){
                        $con = str_replace(' ','&nbsp',$v['content']);
                        $arr[$k]['content'] = str_replace("\r\n",'<br>',$con);
                        $str .="('".$arr[$k]['title']."','".$arr[$k]['content']."','".$time."','21','0','0','11114','0'),";
                    }
                    $str = substr($str, 0, -1);
                    //print_r($arr);exit;
                    //echo $str;
                    $sql = "INSERT INTO `ty_web_article` (`title`, `content`, `add_time`, `category_id`, `visit_times`, `is_delete`, `game_id`, `is_recommend`) VALUES ".$str;
                    $model = new \Admin\AdminIndexModel();
                    $data = $model->opAddNoveData($sql);
                    unset($arr);
                }
                $arr[$i]['title'] = substr($row,5);
                $j++;
            }else {
                $content .= $row;
                $arr[$i]['content'] = $content;
            }
        }
    }

    // 添加落地页
    public function opAddLandingAction(){
        $landingId = isset($this->_postData['landing_id'])? trim($this->_postData['landing_id']):'';
        $landingName = isset($this->_postData['landing_name'])? $this->_postData['landing_name']:'';

        $autoDownload = isset($this->_postData['auto_download'])? $this->_postData['auto_download']:'';
        $downloadTime = isset($this->_postData['download_time'])? $this->_postData['download_time']:'';
        $screenDownload = isset($this->_postData['screen_download'])? $this->_postData['screen_download']:'';
        $downloadUrl = isset($this->_postData['download_url'])? $this->_postData['download_url']:'';
        $company = isset($this->_postData['company'])? $this->_postData['company']:'';

        $selectTemp = isset($this->_postData['select_temp'])? $this->_postData['select_temp']:'';
        $gameId = isset($this->_postData['extra_game_id'])? $this->_postData['extra_game_id']:'';

        $template = '';
        if(!empty($selectTemp)){
            $template = $selectTemp;
        }else if($_FILES['temp']['tmp_name']){
            $tmp_name = $_FILES['temp']['tmp_name'];
            $template = $_FILES['temp']['name'];
            if(file_exists('landing/'.$template)){
                die('文件已存在');
            }
            //echo $tmp_name;exit;
            $res=move_uploaded_file($tmp_name, 'landing/'.$template);//将上传的文件移动到新位置
            if(!$res){
                die('文件上传失败');
            }
        }else {
            die('请选择模板或上传新模板');
        }
        $landingUrl = "http://sdkadmin.wanyouxi.com/landing/".$template.'?v='.$landingId;
        //echo $landingUrl;exit;

        $conf = ['autoDownload'=>$autoDownload,'downloadTime'=>$downloadTime,'screenDownload'=>$screenDownload,'downloadUrl'=>$downloadUrl,'company'=>$company];
        $confJson = json_encode($conf);
        //echo $confJson;exit;
        $param = ['landing_id'=>$landingId,'landing_name'=>$landingName,'landing_url'=>$landingUrl,'template'=>$template,'conf'=>$confJson,'game_id'=>$gameId,'add_time'=>time()];

        $model = new \Admin\AdminIndexModel();
        $data = $model->opAddLanding($param);
        if($data['code']==200){
            echo "<script>window.location.href='/admin/adminindex/landinglist';</script>";die;
        }else {
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }

    // 编辑落地页
    public function opEditLandingAction(){
        $id = isset($this->_postData['id'])? trim($this->_postData['id']):'';
        $landingName = isset($this->_postData['landing_name'])? $this->_postData['landing_name']:'';

        $autoDownload = isset($this->_postData['auto_download'])? $this->_postData['auto_download']:'';
        $downloadTime = isset($this->_postData['download_time'])? $this->_postData['download_time']:'';
        $screenDownload = isset($this->_postData['screen_download'])? $this->_postData['screen_download']:'';
        $downloadUrl = isset($this->_postData['download_url'])? $this->_postData['download_url']:'';
        $company = isset($this->_postData['company'])? $this->_postData['company']:'';

        $selectTemp = isset($this->_postData['select_temp'])? $this->_postData['select_temp']:'';

        $template = '';
        if(!empty($selectTemp)){
            $template = $selectTemp;
        }else if($_FILES['temp']['tmp_name']){
            $tmp_name = $_FILES['temp']['tmp_name'];
            $template = $_FILES['temp']['name'];
            if(file_exists('landing/'.$template)){
                die('文件已存在');
            }
            $res=move_uploaded_file($tmp_name, 'landing/'.$template);//将上传的文件移动到新位置
            if(!$res){
                die('文件上传失败');
            }
        }else {
            die('请选择模板或上传新模板');
        }
        //echo $landingUrl;exit;

        $conf = ['autoDownload'=>$autoDownload,'downloadTime'=>$downloadTime,'screenDownload'=>$screenDownload,'downloadUrl'=>$downloadUrl,'company'=>$company];
        $confJson = json_encode($conf);
        //echo $confJson;exit;
        $param = ['id'=>$id,'landing_name'=>$landingName,'template'=>$template,'conf'=>$confJson];
        //print_r($param);exit;
        $model = new \Admin\AdminIndexModel();
        $data = $model->opEditLanding($param);
        if($data['code']==200){
            echo "<script>window.location.href='/admin/adminindex/landinglist';</script>";die;
        }else {
            echo "<script>alert('编辑失败');history.back();</script>";
        }
    }

    // 删除落地页
    public function opDeleteLandingAction(){
        $param['id'] = isset($this->_getData['id'])?$this->_getData['id']:'';
        if(empty($param['id'])){
            echo "<script>alert('不合法操作');history.back();</script>";
        }
        $model = new \Admin\AdminIndexModel();
        $data = $model->opDeleteLanding($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('删除失败');history.back();</script>";
        }
    }

    // 强制解除用户手机绑定
    public function deleteBindCellphoneAction(){
        $param['account'] = isset($this->_postData['account'])? $this->_postData['account']:'';
        $model= new \Admin\AdminIndexModel();
        $data = $model->deleteBindCellphone($param);
        //print_r($data);exit;
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }elseif($data['code']==400){
            echo "<script>alert('手机号不存在');history.back();</script>";
        }elseif($data['code']==401){
            echo "<script>alert('手机号为用户名，不能解绑');history.back();</script>";
        }else {
            echo "<script>alert('解除绑定失败');history.back();</script>";
        }
    }

    // 添加sdk
    public function uploadSdkAction() {
        $param = $this->_postData;
        $model= new \Admin\AdminIndexModel();
        if(!isset($param['debug']) || $param['debug'] ==null) 
            $param['debug'] = 0;
        $result = $model->is_exists($param['sdk_version'],$param['sdk_type'],$param['debug']);
        if($result){
            echo "<script>alert('已存在该版本的SDK记录');history.back();</script>";
            return false;
        }
        $data = $model->addSdk($param);
        if($data['code']==200){
            echo "<script>history.back();</script>";die;
        }else {
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }

    //添加patch
    public function uploadPatchAction() {
        $param = $this->_postData;
        $tmp = explode("-",$param['sdk_version']);
        unset($param['sdk_version']);
        $param['sdk_type'] = trim($tmp[0]);
        $param['sdk_version'] = trim($tmp[1]);
        $model = new \Admin\AdminIndexModel();
        $data = $model->addPatch($param);
        if($data['code'] == 200) {
            echo "<script>history.back();</script>";die;
        } else {
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }

    //更新sdk
    public function updateSdkAction() {
        $param = $this->_postData;
        $model = new \Admin\AdminIndexModel();
        $result = $model->is_exists($param['sdk_version'],$param['sdk_type'],-1,$param['sdk_url']);
        if($result){
            echo "<script>alert('已存在该版本的SDK记录');history.back();</script>";
            return false;
        }
        $data = $model->updatesdk($param);
        if($data['code'] == 200) {
            echo "<script>history.back();</script>";die;
        } else {
            echo "<script>alert('添加失败');history.back();</script>";
        }
    }

    //上传至ucloud
    public function ucloudAction() {

        $post = $this->_postData;
        //待上传文件的本地路径
        $file = $_FILES['file']['tmp_name'];
        //保存的文件路径
        $key = $_FILES['file']['name'];
        if(isset($post['debug'])) //如果是测试版本，那么在前面加上一个'test_'前缀
            $key = 'test_'.$key;
        $proxy = new \Addons\ucloud\proxy();
        $bucket = 'tfstatic';

        list($data, $err) = $proxy->UCloud_MInit($bucket, $key);
        if ($err)
        {
            echo "error: " . $err->ErrMsg . "\n";
            echo "code: " . $err->Code . "\n";
            exit;
        }
        $uploadId = $data['UploadId'];
        $blkSize  = $data['BlkSize'];
        list($etagList, $err) = $proxy->UCloud_MUpload($bucket, $key, $file, $uploadId, $blkSize);
        if ($err) {
            echo "error: " . $err->ErrMsg . "\n";
            echo "code: " . $err->Code . "\n";
            exit;
        }
        list($data, $err) = $proxy->UCloud_MFinish($bucket, $key, $uploadId, $etagList);
        if ($err) {
            echo "error: " . $err->ErrMsg . "\n";
            echo "code: " . $err->Code . "\n";
            exit;
        }
        $url = "http://".$proxy->UCloud_MakePublicUrl($bucket, $key);
        echo json_encode($url);
    }

    //下载sdk or patch
    public function downloadSdkAction() {
        if (isset($this->_getData['downloadurl'])) {
            $downloadurl = urldecode($this->_getData['downloadurl']);
            $filename = substr($downloadurl,strrpos($downloadurl,'/')+1);
            $file_ext = substr($downloadurl,strrpos($downloadurl,'.')+1);
            //常用的ContentType
            $con_type['txt'] = 'text/plain';
            $con_type['doc'] = 'application/msword';
            $con_type['js'] = 'application/x-javascript';
            $con_type['jpg'] = 'image/jpeg';
            $con_type['html'] = 'text/html';
            $con_type['htm'] = 'text/htm';

            if (isset($con_type[$file_ext])){   //如果是以上的文件扩展名，让浏览器下载文件而并不是打开
                header('Content-type: text/plain');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                readfile($downloadurl);
                exit();
            } else {
                header("Location:".$downloadurl);
            }
        }
    }

    // 获取模板内容
    public function getTemplateContentAction(){
        $template = isset($this->_postData['template'])?$this->_postData['template']:'';
        //$template = isset($this->_getData['template'])?$this->_getData['template']:'';
        //echo $template;exit;
        $file='landing/'.$template;
        if($template=='' || !file_exists($file)){
            $data = ['code'=>201,'message'=>'File not exists','data'=>''];
            $this->returnValue($data);
        }
        $content = file_get_contents($file);
        //$content = htmlspecialchars($content);
        //echo $content;exit;
        $data = ['code'=>200,'message'=>'Success','data'=>$content];
        $this->returnValue($data);
    }

    // 编辑模板内容
    public function opEditTemplateAction(){
        $template = isset($this->_postData['template'])?$this->_postData['template']:'';
        $content = isset($this->_postData['content'])?$this->_postData['content']:'';
        //echo $content;exit;
        $file='landing/'.$template;
        if($template=='' || !file_exists($file)){
            echo "<script>alert('文件不存在');history.back();</script>";
        }
        $res = file_put_contents($file,$content);
        //var_dump($res);exit;
        if($res){
            echo "<script>alert('编辑成功');history.back();</script>";
        }else{
            echo "<script>alert('编辑失败');history.back();</script>";
        }

    }
}

