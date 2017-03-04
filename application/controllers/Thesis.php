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
        // print_r($data);
        if($data['code'] == 200){             
            $this->setWebSession($data);
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
        $param['things_content'] = isset($this->_postData['things_content']) ? $this->_postData['things_content']: '';
        $user_id = isset($this->_postData['user_id']) ? $this->_postData['user_id']: '';
        if(empty($user_id)){
            $data['code'] = 1;
            $data['msg'] = '请先登录！'; 
            echo json_encode($data);
        }
        if($_FILES && $_FILES['things_img']['tmp_name']){
            $tmp_name = $_FILES['things_img']['tmp_name'];
            $template = $_FILES['things_img']['name'];
            $res = move_uploaded_file($tmp_name, 'uploads/things_img/'.$template);//将上传的文件移动到新位置
            if(!$res){
                $data['code'] = 1;
                $data['msg'] = '图片上传失败，请重试！';
                echo json_encode($data);
            }else{
                $param['things_img'] = '/uploads/things_img/' . $template;
            }
        }else{
            $param['things_img'] = '';
        }       
        
        // print_r($param);die;
        $model = new \Web\ThesisModel();
        $data = $model->publishThings($param,$user_id);

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
        $register_time = isset($this->_getData['time']) ? $this->_getData['time'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getUserInfo($uid,$register_time);

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
    //获取单个用户发表的趣事
    public function getUserThingAction(){
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : $this->page;
        $other_user = isset($this->_getData['other_user']) ? $this->_getData['other_user'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getUserThing($user_id,$other_user,$page,$this->_count);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取用户趣事成功！'; 
            $data['data'] = $data['data'];
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取用户趣事失败，请重试'; 
        }
        echo json_encode($data);
    }    

    //获取单个用户发表的评论
    public function getUserCommentAction(){
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : $this->page;
        $model = new \Web\ThesisModel();
        $data = $model->getUserComment($user_id,$page,$this->_count);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取单个用户评论成功！'; 
            $data['data'] = $data['data'];
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取用户评论失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取回复我的评论
    public function getUserReplyAction(){
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : $this->page;
        $model = new \Web\ThesisModel();
        $data = $model->getUserReply($user_id,$page,$this->_count);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取单个用户评论成功！'; 
            $data['data'] = $data['data'];
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取用户评论失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取单个用户收藏的趣事
    public function getUserFavoriteAction(){
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : $this->page;
        $other_user = isset($this->_getData['other_user']) ? $this->_getData['other_user'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getUserFavorite($user_id,$other_user,$page,$this->_count);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取用户收藏成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 0;
            $data['msg'] = '用户收藏列表为空！'; 
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取用户收藏失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取热门趣事
    public function getHotThingsListAction(){
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : '';
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getHotThingsList($page,$this->_count,$user_id);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取热门趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 0;
            $data['msg'] = '热门趣事列表为空！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取热门趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取新鲜趣事
    public function getFreshThingsListAction(){
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : '';
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getFreshThingsList($page,$this->_count,$user_id);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取新鲜趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 0;
            $data['msg'] = '新鲜趣事列表为空！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取新鲜趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取纯文趣事
    public function getWordThingsListAction(){
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : '';
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getWordThingsList($page,$this->_count,$user_id);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取纯文趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 0;
            $data['msg'] = '纯文趣事列表为空！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取纯文趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取带图趣事
    public function getImageThingsListAction(){
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : '';
        $user_id = isset($this->_getData['user_id']) ? $this->_getData['user_id'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getImageThingsList($page,$this->_count,$user_id);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取带图趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 0;
            $data['msg'] = '带图趣事列表为空！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取带图趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //踩
    public function trampDownAction(){
        // $this->checkIsLogin();
        $things_id = isset($this->_postData['things_id']) ? $this->_postData['things_id'] : '';
        $uid = isset($this->_postData['user_id']) ? $this->_postData['user_id'] : $this->_uid;
        $model = new \Web\ThesisModel();
        $data = $model->trampDown($things_id,$uid);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '踩趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 1;
            $data['msg'] = '您已踩过该趣事！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '踩趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //点赞
    public function praiseUpAction(){
        // $this->checkIsLogin();
        $things_id = isset($this->_postData['things_id']) ? $this->_postData['things_id'] : '';
        $uid = isset($this->_postData['user_id']) ? $this->_postData['user_id'] : $this->_uid;
        $model = new \Web\ThesisModel();
        $data = $model->praiseUp($things_id,$uid);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '点赞趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 1;
            $data['msg'] = '您已点赞过该趣事！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '点赞趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //收藏趣事
    public function favoriteAction(){
        // $this->checkIsLogin();
        $things_id = isset($this->_postData['things_id']) ? $this->_postData['things_id'] : '';
        $uid = isset($this->_postData['user_id']) ? $this->_postData['user_id'] : $this->_uid;
        $model = new \Web\ThesisModel();
        $data = $model->favorite($things_id,$uid);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '收藏趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 1;
            $data['msg'] = '您已收藏过该趣事！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '收藏趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

     //取消收藏趣事
    public function cancelFavoriteAction(){
        $things_id = isset($this->_postData['things_id']) ? $this->_postData['things_id'] : '';
        $uid = isset($this->_postData['user_id']) ? $this->_postData['user_id'] : $this->_uid;
        $model = new \Web\ThesisModel();
        $data = $model->cancelFavorite($things_id,$uid);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '取消收藏趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 1;
            $data['msg'] = '您还未收藏过该趣事！'; 
            $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '取消收藏趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取单条趣事信息
    public function getThingInfoAction(){
        $thing_id = isset($this->_getData['thing_id']) ? $this->_getData['thing_id'] : '';
        $uid = isset($this->_getData['uid']) ? $this->_getData['uid'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getThingInfo($thing_id,$uid);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取单条趣事成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 1;
            $data['msg'] = '没有该趣事！'; 
            // $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取单条趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //获取评论列表
    public function getCommentsListAction(){
        $page = isset($this->_getData['page']) ? $this->_getData['page'] : '';
        $thing_id = isset($this->_getData['thing_id']) ? $this->_getData['thing_id'] : '';
        $model = new \Web\ThesisModel();
        $data = $model->getCommentsList($page,$thing_id,$this->_count);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '获取评论列表成功！'; 
            $data['data'] = $data['data'];
        }elseif($data['code'] == 201){ 
            $data['code'] = 0;
            $data['msg'] = '评论列表为空！'; 
            // $data['data'] = array();
        }else{
            $data['code'] = 1;
            $data['msg'] = '获取评论列表失败，请重试'; 
        }
        echo json_encode($data);
    }

    //评论趣事
    public function commentAction(){
        $things_id = isset($this->_postData['thing_id']) ? $this->_postData['thing_id'] : '';
        $uid = isset($this->_postData['user_id']) ? $this->_postData['user_id'] : '';
        $content = isset($this->_postData['content']) ? $this->_postData['content'] : '';
        if(empty($content)){
            $data['code'] = 1;
            $data['msg'] = '评论内容不能为空！'; 
            $data['data'] = $data['data'];
            echo json_encode($data);
        }
        if(empty($uid)){
            $data['code'] = 1;
            $data['msg'] = '用户id不能为空！'; 
            $data['data'] = $data['data'];
            echo json_encode($data);
        }
        $model = new \Web\ThesisModel();
        $data = $model->comment($things_id,$uid,$content);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '评论趣事成功！'; 
            $data['data'] = $data['data'];
        }else{
            $data['code'] = 1;
            $data['msg'] = '评论趣事失败，请重试'; 
        }
        echo json_encode($data);
    }

    //回复评论
    public function replyCommentAction(){
        $param['reply_user'] = isset($this->_postData['reply_user']) ? $this->_postData['reply_user'] : '';
        $param['reply_user_name'] = isset($this->_postData['reply_user_name']) ? $this->_postData['reply_user_name'] : '';
        $param['replied_user'] = isset($this->_postData['replied_user']) ? $this->_postData['replied_user'] : '';
         $param['replied_user_name'] = isset($this->_postData['replied_user_name']) ? $this->_postData['replied_user_name'] : '';
        $param['reply_content'] = isset($this->_postData['reply_content']) ? $this->_postData['reply_content'] : '';
        $param['comment_id'] = isset($this->_postData['comment_id']) ? $this->_postData['comment_id'] : '';
        $param['things_id'] = isset($this->_postData['things_id']) ? $this->_postData['things_id'] : '';

        $model = new \Web\ThesisModel();
        $data = $model->replyComment($param);
        if($data['code'] == 200){ 
            $data['code'] = 0;
            $data['msg'] = '回复评论成功！'; 
            $data['data'] = $data['data'];
        }else{
            $data['code'] = 1;
            $data['msg'] = '回复评论失败，请重试'; 
        }
        echo json_encode($data);
    }

    //平台登出
    public function opLogoutAction(){
        $this->unsetWebSession();
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