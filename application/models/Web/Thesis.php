<?php
namespace Web;
class ThesisModel extends \Core\BaseModels {    
    public function handleUser($funny_thing,$unfunny_thing,$thing){
        foreach ($thing as $t_key => $t_val) {
            foreach ($funny_thing as $f_key => $f_val) {
                if($thing[$t_key]['things_id'] == $funny_thing[$f_key]['things_id']){
                    $thing[$t_key]['is_praise'] = 1;
                    break;
                }else{
                    $thing[$t_key]['is_praise'] = 0;                     
                }

            }
            foreach ($unfunny_thing as $un_key => $un_val) {
                if($thing[$t_key]['things_id'] == $unfunny_thing[$un_key]['things_id']){
                    $thing[$t_key]['is_tramp'] = 1;
                    break;
                }else{
                    $thing[$t_key]['is_tramp'] = 0;
                }          
            }           
            $thing[$t_key]['comment_param'] = $thing[$t_key]['comment_param'] . '_' . $thing[$t_key]['is_praise'] . '_' . $thing[$t_key]['is_tramp'];
        }
        // print_r($thing);die;
        return $thing;
    }

     //注册
    public function register($param){
        $options['table'] = 'user';
        $photo = '/uploads/avatar/default-avatar.png';
        $tmpData = array('user_name'=>'?','user_password'=>'?','user_photo'=>'?','register_time'=>'?');
        $options['param'] = array($param['username'],md5($param['password']),$photo,time());
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
        $options['where'] = array('user_name'=>'?','user_password'=>'?','is_delete'=>'?');
        $options['param'] = array($param['username'],md5($param['password']),0);
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
        if(!empty($param['things_image'])){
            $has_img = 1;
        }else{
            $has_img = 0;
        }
        $tmpData = array('things_content'=>'?','things_image'=>'?','is_anonymous'=>'?','publish_time'=>'?','user_id'=>'?','has_img'=>'?');
        $options1['table'] = 'things';        
        $options1['param'] = array($param['things_content'],$param['things_img'],$param['is_anonymous'],time(),$user_id,$has_img);
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

    //修改用户名
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

    // 获取趣事
    public function getFunnyThingsList($page,$count,$uid){
        //用户        
        $options['table'] = 'user';
        $user_list = $this->db->select($options); 

        if($uid > 0){
            //该用户点过赞的趣事
            $options11['table'] = 'funny_things';
            $options11['where'] = array('user_id'=>'?');
            $options11['param'] =  array($uid); 
            $funny_list = $this->db->select($options11);

            // print_r($funny_list);die;
             //该用户踩过的趣事
            $options12['table'] = 'unfunny_things';
            $options12['where'] = array('user_id'=>'?');
            $options12['param'] =  array($uid); 
            $unfunny_list = $this->db->select($options12);
            // print_r($unfunny_list);die;

        }
        
        //热门 
        $options1['table'] = 'things';
        $options1['where'] = array('is_approval'=>'?');
        $options1['param'] =  array(1);    
        $options1['limit'] = ($page-1)*$count.','.$count; 
        $totalNum1=$this->db->count($options1);
        $totalPage1=ceil($totalNum1/$count);   
        $options1['order'] = 'comment_num desc';
        $hot_things = $this->db->select($options1);   

        foreach($hot_things as $hot_k=>$hot_v){
            $hot_things[$hot_k]['comment_param'] = $hot_things[$hot_k]['things_id'];
            foreach($user_list as $user_k=>$user_v){
                if($hot_things[$hot_k]['user_id'] = $user_list[$user_k]['user_id']){
                    $hot_things[$hot_k]['user_info'] = $user_list[$user_k];
                }                
            }        
        }
        if($uid > 0){
            $hot_things = $this->handleUser($funny_list,$unfunny_list,$hot_things);
        }
        $hot_things = array('totalPage'=>$totalPage1,'totalNum'=>$totalNum1,'page'=>$page,'list'=>$hot_things);


        //新鲜
        $options2['table'] = 'things';
        $options2['order'] = 'publish_time desc';
        $options2['limit'] = ($page-1)*$count.','.$count; 
        $totalNum2=$this->db->count($options2);
        $totalPage2=ceil($totalNum2/$count); 
        $options2['where'] = array('is_approval'=>'?');
        $options2['param'] =  array(1);  
        $fresh_things = $this->db->select($options2);       
        foreach($fresh_things as $fresh_k=>$fresh_v){
            $fresh_things[$fresh_k]['comment_param'] = $fresh_things[$fresh_k]['things_id'];
            foreach($user_list as $user_k=>$user_v){
                if($fresh_things[$fresh_k]['user_id'] = $user_list[$user_k]['user_id']){
                    $fresh_things[$fresh_k]['user_info'] = $user_list[$user_k];
                }
            }
           
        }
        if($uid > 0){
            $fresh_things = $this->handleUser($funny_list,$unfunny_list,$fresh_things);
        }
        $fresh_things = array('totalPage'=>$totalPage2,'totalNum'=>$totalNum2,'page'=>$page,'list'=>$fresh_things);

        //带图 
        $options3['table'] = 'things';
        $options3['where'] = array('is_approval'=>'?','has_img'=>'?');
        $options3['param'] =  array(1,1);    
        $options3['limit'] = ($page-1)*$count.','.$count; 
        $totalNum3=$this->db->count($options3);
        $totalPage3=ceil($totalNum3/$count);   
        $options3['order'] = 'comment_num desc';
        $img_things = $this->db->select($options3);   

        foreach($img_things as $img_k=>$img_v){
            $img_things[$img_k]['comment_param'] = $img_things[$img_k]['things_id'];            
            foreach($user_list as $user_k=>$user_v){
                if($img_things[$img_k]['user_id'] = $user_list[$user_k]['user_id']){
                    $img_things[$img_k]['user_info'] = $user_list[$user_k];
                }                
            }
        
        }
        if($uid > 0){
            $img_things = $this->handleUser($funny_list,$unfunny_list,$img_things);
        }
        $img_things = array('totalPage'=>$totalPage3,'totalNum'=>$totalNum3,'page'=>$page,'list'=>$img_things);

        //纯文 
        $options4['table'] = 'things';
        $options4['where'] = array('is_approval'=>'?','has_img'=>'?');
        $options4['param'] =  array(1,0);    
        $options4['limit'] = ($page-1)*$count.','.$count; 
        $totalNum4=$this->db->count($options4);
        $totalPage4=ceil($totalNum4/$count);   
        $options4['order'] = 'comment_num desc';
        $word_things = $this->db->select($options4);   

        foreach($word_things as $word_k=>$word_v){
            $word_things[$word_k]['comment_param'] = $word_things[$word_k]['things_id'];                        
            foreach($user_list as $user_k=>$user_v){
                if($word_things[$word_k]['user_id'] = $user_list[$user_k]['user_id']){
                    $word_things[$word_k]['user_info'] = $user_list[$user_k];
                }                
            }
        
        }
        if($uid > 0){
            $word_things = $this->handleUser($funny_list,$unfunny_list,$word_things);
        }
        $word_things = array('totalPage'=>$totalPage4,'totalNum'=>$totalNum4,'page'=>$page,'list'=>$word_things);

        $list = array(
            'fresh_things'=>$fresh_things,//新鲜
            'hot_things'=>$hot_things,//热门    
            'img_things'=>$img_things,//带图
            'word_things'=>$word_things,//纯文字
        );
        return $this->returnResult(200,$list);
    }
    
    //点赞趣事
    public function praiseUp($things_id,$user_id){
        $options['table'] = 'funny_things';
        $options['where'] = array('user_id'=>'?','things_id'=>'?');
        $options['param'] = array($user_id,$things_id);
        $res = $this->db->find($options);        

        if(empty($res)){
            $options1['table'] = 'funny_things';
            $tmpData = array('user_id'=>'?','things_id'=>'?');
            $options1['param'] = array($user_id,$things_id);
            $res1 = $this->db->add($tmpData,$options1);

            $updateSql = 'update things set funny_num = funny_num + 1 where things_id =  '.$things_id;
            $info = $this->db->create($updateSql);
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(201);            
        }    
    }

    //踩趣事
    public function trampDown($things_id,$user_id){
        $options['table'] = 'unfunny_things';
        $options['where'] = array('user_id'=>'?','things_id'=>'?');
        $options['param'] = array($user_id,$things_id);
        $res = $this->db->find($options);
        if(empty($res)){
            $options1['table'] = 'unfunny_things';
            $tmpData = array('user_id'=>'?','things_id'=>'?');
            $options1['param'] = array($user_id,$things_id);
            $res1 = $this->db->add($tmpData,$options1);

            $updateSql = 'update things set unfunny_num = unfunny_num + 1 where things_id =  '.$things_id;
            $info = $this->db->create($updateSql);
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(201);            
        }       
    }

    //收藏趣事
    public function favorite($things_id,$user_id){
        $options['table'] = 'favorite_things';
        $options['where'] = array('user_id'=>'?','things_id'=>'?');
        $options['param'] = array($user_id,$things_id);
        $res = $this->db->find($options);
        if(empty($res)){
            $options1['table'] = 'favorite_things';
            $tmpData = array('user_id'=>'?','things_id'=>'?');
            $options1['param'] = array($user_id,$things_id);
            $res1 = $this->db->add($tmpData,$options1);

            $updateSql = 'update things set favorite_num = favorite_num + 1 where things_id =  '.$things_id;
            $info = $this->db->create($updateSql);
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(201);            
        }     
    }
    //根据趣事id获取单条趣事
    public function getThingInfo($thing_id,$user_id){
        $options['table'] = 'things';
        $options['where'] = array('things_id'=>'?');
        $options['param'] = array($thing_id);
        $thing = $this->db->find($options);

        if(empty($thing)){
            return $this->returnResult(201);
        }
        // print_r($thing['user_id']);die;
        $options1['table'] = 'user';
        $options1['field'] = 'user_name,user_photo';
        $options1['where'] = array('user_id'=>'?');
        $options1['param'] = array($thing['user_id']);
        $user = $this->db->find($options1);
        if(empty($user)){
            return $this->returnResult(201);
        }
        $thing['userInfo'] = $user;
        return $this->returnResult(200,$thing);
    }

    //获取评论列表
    public function getCommentsList($page,$thing_id,$count){
        $options['table'] = 'comment';
        $options['where'] = array('things_id'=>'?');
        $options['param'] = array($thing_id);
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'comment_time asc';
        $comments = $this->db->select($options); 

        if(empty($comments)){
            return $this->returnResult(201);
        }
        // print_r($comments);die;
        // $options1['table'] = 'comment_user';
        // // $options1['field'] = 'user_id,user_name,user_photo';
        // $options1['where'] = array('thing_id'=>'?');
        // $options1['param'] = array($thing_id);
        // $comment_users = $this->db->find($options1);
        // if(empty($comment_users)){
        //     return $this->returnResult(201);
        // }

        // $options2['table'] = 'user';
        // $options2['field'] = 'user_id,user_name,user_photo';       
        // $users = $this->db->select($options2);
        // if(empty($users)){
        //     return $this->returnResult(201);
        // }

        // $comments.foreach ($comment as $c_key => $v_value) {
        //     $users.foreach ($user as $u_key => $u_value) {
        //         if($comment[$c_key]['user_id'] ===  $user[$u_key]['user_id']){}
        //     }
        //     $users.foreach ($user as $u_key => $u_value) {
        //         if($comment[$c_key]['user_id'] ===  $user[$u_key]['user_id']){}
        //     }
        // }
        $data = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$comments);
        return $this->returnResult(200,$data);
    }

    //评论趣事
    public function comment($things_id,$user_id,$content){
        $options['table'] = 'user';
        $tmpData = array('user_id'=>'?');
        $options['param'] = array($user_id);
        $user = $this->db->find($options);

        $options1['table'] = 'comment_user';
        $tmpData1 = array('user_id'=>'?','things_id'=>'?');
        $options1['param'] = array($user_id,$things_id);
        $res = $this->db->add($tmpData1,$options1);

        $options1['table'] = 'things';
        $updateSql = 'update things set comment_num = comment_num + 1 where things_id =  '.$things_id;
        $data = $this->db->create($updateSql);

        $options2['table'] = 'comment';
        $tmpData2 = array('user_id'=>'?','user_name'=>'?','user_photo'=>'?','things_id'=>'?','content'=>'?','comment_time'=>'?');
        $options2['param'] = array($user_id,$user['user_name'],$user['user_photo'],$things_id,$content,time());
        $info = $this->db->add($tmpData2,$options2);
        if($res !== FALSE && $info !== FALSE){           
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(4000);            
        }    
    }
}
