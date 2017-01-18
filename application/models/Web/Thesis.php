<?php
namespace Web;
class ThesisModel extends \Core\BaseModels {

     //注册
    public function register($param){
        $options['table'] = 'user';
        $photo = '/uploads/avatar/default-avatar.png';
        $tmpData = array('user_name'=>'?','user_password'=>'?','user_photo'=>'?');
        $options['param'] = array($param['username'],md5($param['password']),$photo);
        $status = $this->db->add($tmpData,$options);
        // print_r($info);exit;
        if($status !== FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    //登陆
    public function login($param){
        $options['table'] = 'user';
        $options['where'] = array('user_name'=>'?','user_password'=>'?');
        $options['param'] = array($param['username'],md5($param['password']));
        // print_r($options);exit;
        $info = $this->db->find($options);
        // print_r($info);exit;
        if(!empty($info)){
            return $this->returnResult(200,$info);
        }else {
            return $this->returnResult(201);
        }
    }

    //重置密码
    public function reset($param){       
        $options['table'] = 'user';
        $options['where'] = array('user_name'=>'?');
        $options['param'] = array($param['username']);
        $info = $this->db->find($options);
        // print_r($info);exit;
        if(empty($info)){
            return $this->returnResult(201);
        }
        $tmpData = array('user_password'=>'?');
        $options1['table']='user';
        $options1['where'] = array('user_name'=>'?');
        $options1['param']=array(md5($param['password']),$param['username']);
        $status=$this->db->save($tmpData,$options1);
        if($status!=FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }

    //发表趣事
    public function publishThings($param,$user_id){ 
        $options['table'] = 'user';        
        $options['where'] = array('user_id'=>'?');
        $options['param'] = array($user_id);
        $result = $this->db->find($options);
        if(empty($result)){
            return $this->returnResult(201);
        }
        $tmpData = array('things_content'=>'?','things_image'=>'?','is_anonymous'=>'?','publish_time'=>'?','user_id'=>'?');
        $options1['table'] = 'things';        
        $options1['param'] = array($param['things_content'],$param['things_img'],$param['is_anonymous'],time(),$user_id);
        $info = $this->db->add($tmpData,$options1);

        if($info!=FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }

    //获取用户信息
    public function getUserInfo($user_id){
        $options['table'] = 'user';
        $options['where'] = array('user_id'=>'?');
        $options['param'] = array($user_id);
        $info = $this->db->find($options);
        // print_r($info);exit;
        if(empty($info)){
            return $this->returnResult(201);
        }else{
            return $this->returnResult(200,$info);
        }
        
    }


    //修改头像
    public function changeAvatar($user_id,$photo){
        $options['table'] = 'user';
        $tmpData = array('user_photo'=>'?');
        $options['where'] = array('user_id'=>'?');
        $options['param'] = array($photo,$user_id);
        $info = $this->db->save($tmpData,$options);
        if($info !== FALSE){
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(201);            
        }
    }

    //修改密码
    public function changePwd($param){
        $options['table'] = 'user';
        $tmpData = array('user_password'=>'?');
        $options['where'] = array('user_id'=>'?','user_password'=>'?');
        $options['param'] = array(md5($param['password']),$param['user_id'],md5($param['originPwd']));
        $info = $this->db->save($tmpData,$options);
        if($info != FALSE){
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(201);            
        }
    }
    public function changeUname($param){
        $options['table'] = 'user';
        $tmpData = array('user_name'=>'?');
        $options['where'] = array('user_id'=>'?');
        $options['param'] = array($param['uname'],$param['user_id']);
        $info = $this->db->save($tmpData,$options);
        if($info != FALSE){
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(201);            
        }
    }

    // 获取热门趣事
    public function getHotThings(){
        $options['table'] = 'things';
        $options['order'] = 'comment_num desc';
        $hot_things = $this->db->select($options);       
        if(empty($hot_things)){
            return $this->returnResult(201);die;
        }else{
            return $this->returnResult(200,$hot_things);
        }
    }

    //获取新鲜趣事
    public function getFreshThings(){
        $options['table'] = 'things';
        $options['order'] = 'publish_time desc';
        $fresh_things = $this->db->select($options);       
        if(empty($fresh_things)){
            return $this->returnResult(201);die;
        }else{
            return $this->returnResult(200,$fresh_things);
        }
    }
    
     //退出登录
    public function logoutAction(){
        $this->unsetOauthAdminSession();
    }

    //获取文字趣事
    public function getWordThings($type){ 
        $options['table'] = 'things';
        $options['where'] = array('things_type'=>'?');
        $options['order'] = 'publish_time desc';
        $options['param'] = array($type);
        $word_things = $this->db->select($options);       
        if(empty($word_things)){
            return $this->returnResult(201);die;
        }else{
            return $this->returnResult(200,$fresh_things);
        }
    }

    //获取图片趣事
    public function getPictureThings($type){ 
        $options['table'] = 'things';
        $options['where'] = array('things_type'=>'?');
        $options['order'] = 'publish_time desc';
        $options['param'] = array($type);
        $picture_things = $this->db->select($options);       
        if(empty($picture_things)){
            return $this->returnResult(201);die;
        }else{
            return $this->returnResult(200,$picture_things);
        }       

    }

   

}
