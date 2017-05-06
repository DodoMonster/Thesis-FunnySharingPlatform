<?php
namespace Web;
class ThesisModel extends \Core\BaseModels {    
    public function handleUser($funny_thing,$unfunny_thing,$favorite_thing,$thing){
        foreach ($thing as $t_key => $t_val) {
            $thing[$t_key]['publish_time'] = date('Y-m-d H:i:s',$thing[$t_key]['publish_time']);
            if(!empty($funny_thing)){
                foreach ($funny_thing as $f_key => $f_val) {
                    if($thing[$t_key]['things_id'] == $funny_thing[$f_key]['things_id']){
                        $thing[$t_key]['is_praise'] = 1;
                        break;
                    }else{
                        $thing[$t_key]['is_praise'] = 0;                     
                    }
                }
            }else{
                $thing[$t_key]['is_praise'] = 0;                     
            }
            if(!empty($unfunny_things)){
                foreach ($unfunny_thing as $un_key => $un_val) {
                    if($thing[$t_key]['things_id'] == $unfunny_thing[$un_key]['things_id']){
                        $thing[$t_key]['is_tramp'] = 1;
                        break;
                    }else{
                        $thing[$t_key]['is_tramp'] = 0;
                    }          
                } 
            }else{
                $thing[$t_key]['is_tramp'] = 0;
            }  
            if(!empty($favorite_thing)){            
                foreach ($favorite_thing as $fa_key => $fa_val) {
                    if($thing[$t_key]['things_id'] == $favorite_thing[$fa_key]['things_id']){
                        $thing[$t_key]['is_favorite'] = 1;
                        break;
                    }else{
                        $thing[$t_key]['is_favorite'] = 0;
                    }          
                } 
            }else{
                $thing[$t_key]['is_favorite'] = 0;
            } 
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
        $options['field'] = array('user_id','user_name','user_photo','register_time');
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
        $tmpData = array('things_content'=>'?','things_image'=>'?','publish_time'=>'?','user_id'=>'?','has_img'=>'?');
        $options1['table'] = 'things';        
        $options1['param'] = array($param['things_content'],$param['things_img'],time(),$user_id,$has_img);
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
        $options['field'] = array('user_id','user_name','user_photo','register_time');
        $options['param'] = array($user_id);
        $info = $this->db->find($options);
        // print_r($info);exit;

        $options1['table'] = 'things';
        $options1['where'] = array('user_id'=>'?');
        $options1['field'] = array('comment_num','funny_num','favorite_num');
        $options1['param'] = array($user_id);
        $num = $this->db->select($options1);

        $funny_num = 0;
        $comment_num = 0;
        $favorite_num = 0;
        $now_time = (time() - $info['register_time'] ) / 86400;
        if(!empty($num)){
            foreach ($num as $key => $value) {
                $funny_num = $value['funny_num'] + $funny_num;
                $comment_num = $value['comment_num'] + $comment_num;
                $favorite_num = $value['favorite_num'] + $favorite_num;
            }
        }
        
        // if(empty($info)){
        //     return $this->returnResult(201,$now_time);
        // }else{
            $info['funny_num'] = $funny_num;
            $info['comment_num'] = $comment_num;
            $info['favorite_num'] = $favorite_num;
            $info['old'] = $now_time;
            return $this->returnResult(200,$info);
        // }
        
    }

    //获取单个用户发表的趣事
    public function getUserThing($user_id,$other_user,$page,$count){        
        $option['table'] = 'things';
        $option['where'] = array('user_id'=>'?','is_approval'=>'?');
        $option['param'] = array($user_id,1);
        $option['limit'] = ($page-1)*$count.','.$count;
        $totalNum = $this->db->count($option);
        $totalPage = ceil($totalNum/$count);   
        $option['order'] = 'publish_time desc';
        // print_r($option);
        $result = $this->db->select($option);
        if(empty($result)){
            return $this->returnResult(201);
        }

        $option1['table'] = 'funny_things';
        $option1['where'] = array('user_id'=>'?');
        $option1['param'] = array($other_user); 
        // print_r($option);
        $funny_things = $this->db->select($option1);
        // print_r($funny_things);

        $option2['table'] = 'unfunny_things';
        $option2['where'] = array('user_id'=>'?');
        $option2['param'] = array($other_user); 
        // print_r($option);
        $unfunny_things = $this->db->select($option2);
        // print_r($unfunny_things);

        $option3['table'] = 'favorite_things';
        $option3['where'] = array('user_id'=>'?');
        $option3['param'] = array($other_user); 
        // print_r($option);
        $favorite_things = $this->db->select($option3);

        $result = $this->handleUser($funny_things,$unfunny_things,$favorite_things,$result);
        // print_r($result);
        $list = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$result);
        return $this->returnResult(200,$list);
    }

    //获取单个用户发表的评论
    public function getUserComment($user_id,$page,$count){
        $options['table'] = 'comment as A';
        $options['join'] = ['things as B on A.things_id = B.things_id','user as C on B.user_id = C.user_id'];
        $options['where'] = array('A.user_id'=>'?');
        $options['param'] =  array($user_id);    
        $options['limit'] = ($page-1)*$count.','.$count; 
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'comment_time desc';
        // print_r($options);
        $comment = $this->db->select($options);
        // print_r($comment);
        if(!empty($comment)){
            foreach ($comment as $key => $value) {
                $comment[$key]['comment_time'] = date('Y-m-d H:i:s',$value['comment_time']);
                $comment[$key]['publish_time'] = date('Y-m-d H:i:s',$value['publish_time']);
                $comment[$key]['month'] = date('m',$value['comment_time']);
                $comment[$key]['date'] = date('d',$value['comment_time']);                
            }
        }
        $list = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$comment);
        return $this->returnResult(200,$list); 
    }

    //获取获取回复我的评论
    public function getUserReply($user_id,$page,$count){
        $options['table'] = 'reply as A';
        $options['join'] = ['things as B on A.things_id = B.things_id','user as C on C.user_id = B.user_id'];
        $options['where'] = array('A.replied_user'=>'?');
        $options['param'] =  array($user_id);    
        $options['limit'] = ($page-1)*$count.','.$count; 
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'A.reply_time desc';
        // print_r($options);
        $reply = $this->db->select($options);
        // print_r($reply);
        if(!empty($reply)){
            foreach ($reply as $key => $value) {
                $reply[$key]['reply_time'] = date('Y-m-d H:i:s',$value['reply_time']);
                $reply[$key]['publish_time'] = date('Y-m-d H:i:s',$value['publish_time']);
                $reply[$key]['month'] = date('m',$value['reply_time']);
                $reply[$key]['date'] = date('d',$value['reply_time']);                
            }
        }
        $options1['table'] = 'comment as A';
        $options1['join'] = ['things as B on A.things_id = B.things_id','user as C on C.user_id = B.user_id'];
        $options1['where'] = array('A.user_id'=>'?');
        $options1['param'] =  array($user_id);    
        $options1['limit'] = ($page-1)*$count.','.$count; 
        $totalNum1 = $this->db->count($options1);
        $totalPage1 = ceil($totalNum1/$count);   
        $options1['order'] = 'A.comment_time desc';
        $comment = $this->db->select($options1);
        if(!empty($comment)){
            foreach ($comment as $key => $value) {
                $comment[$key]['comment_time'] = date('Y-m-d H:i:s',$value['comment_time']);
                $comment[$key]['publish_time'] = date('Y-m-d H:i:s',$value['publish_time']);
                $comment[$key]['month'] = date('m',$value['comment_time']);
                $comment[$key]['date'] = date('d',$value['comment_time']);                
            }
        }
        $list = array('totalPage'=>$totalPage,'totalNum'=>$totalNum + $totalNum1,'page'=>$page,'reply'=>$reply,'comment'=>$comment);
        return $this->returnResult(200,$list); 
    }

    //获取单个用户收藏的趣事
    public function getUserFavorite($user_id,$other_user,$page,$count){  
        $option['table'] = 'things as A';

        $option['join'] = 'favorite_things as B on A.things_id = B.things_id';
        $option['where'] = array('B.user_id'=>'?','A.is_approval'=>'?');
        $option['param'] = array($user_id,1); 
        $option['limit'] = ($page-1)*$count.','.$count;
        $totalNum = $this->db->count($option);        
        $totalPage = ceil($totalNum/$count);        
        $option['order'] = 'publish_time desc';
        $res = $this->db->select($option);
        if(empty($res)){
            return $this->returnResult(201);
        }
        // print_r($result);
        $option1['table'] = 'funny_things';
        $option1['where'] = array('user_id'=>'?');
        $option1['param'] = array($other_user); 
        // print_r($option);
        $funny_things = $this->db->select($option1);
        // print_r($funny_things);

        $option2['table'] = 'unfunny_things';
        $option2['where'] = array('user_id'=>'?');
        $option2['param'] = array($other_user); 
        // print_r($option);
        $unfunny_things = $this->db->select($option2);
        // print_r($unfunny_things);

        $option3['table'] = 'favorite_things';
        $option3['where'] = array('user_id'=>'?');
        $option3['param'] = array($other_user); 
        // print_r($option);
        $favorite_things = $this->db->select($option3);

        $result = $this->handleUser($funny_things,$unfunny_things,$favorite_things,$res);
        // print_r($result);
        $list = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$result);
        return $this->returnResult(200,$list);
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
        $options['where'] = array('user_name'=>'?');
        $options['param'] = array($param['uname']);
        $res = $this->db->find($options);
        if(!empty($res)){
            return $this->returnResult(401,$res);    
        }

        $options1['table'] = 'user';
        $tmpData = array('user_name'=>'?');
        $options1['where'] = array('user_id'=>'?');
        $options1['param'] = array($param['uname'],$param['user_id']);
        $info = $this->db->save($tmpData,$options1);

        
        // $updateSql1 = 'update reply set reply_user_name = '.$param['uname'].' where reply_user = '.$param['user_id'];
        // // print_r($updateSql1);
        // $res1 = $this->db->create($updateSql1);
        // $updateSql2 = 'update reply set replied_user_name = '.$param['uname'].' where replied_user = '.$param['user_id'];
        // // print_r($updateSql2);
        // $res2 = $this->db->create($updateSql2);
        if($info != FALSE){
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(201);            
        }
    }

    //获取热门趣事
    public function getHotThingsList($page,$count,$uid){       
        $funny_list = [];
        $unfunny_list = [];
        $favorite_list = [];
        if($uid > 0){
            $other_list = $this->getOtherThings($uid);
            $funny_list = $other_list['funny_things'];
            $unfunny_list = $other_list['unfunny_things'];            
            $favorite_list = $other_list['favorite_things'];
        }
        
        //热门 
        $options['table'] = 'things as A';
        $options['where'] = array('A.is_approval'=>'?');
        $options['join'] = 'user as B on A.user_id = B.user_id';
        $options['param'] =  array(1);    
        $options['limit'] = ($page-1)*$count.','.$count; 
        // print_r($options);
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'comment_num desc';
        $hot_things = $this->db->select($options); 

        if(!empty($hot_things)){
            $hot_things = $this->handleUser($funny_list,$unfunny_list,$favorite_list,$hot_things);
        }
        $hot_things = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$hot_things);

        return $this->returnResult(200,$hot_things);
    }
    
    //获取新鲜趣事
    public function getFreshThingsList($page,$count,$uid){       
        $funny_list = [];
        $unfunny_list = [];
        $favorite_list = [];
        if($uid > 0){
            $other_list = $this->getOtherThings($uid);
            // print_r($otherThing);
            $funny_list = $other_list['funny_things'];
            $unfunny_list = $other_list['unfunny_things'];            
            $favorite_list = $other_list['favorite_things'];
            // print_r($favorite_list);die;
            // echo "string";
        }
        
        //热门 
        $options['table'] = 'things as A';
        $options['where'] = array('A.is_approval'=>'?');
        $options['join'] = 'user as B on A.user_id = B.user_id';
        $options['param'] =  array(1);    
        $options['limit'] = ($page-1)*$count.','.$count; 
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'publish_time desc';
        $fresh_things = $this->db->select($options); 
        // print_r($fresh_things);

        if(!empty($fresh_things)){
            $fresh_things = $this->handleUser($funny_list,$unfunny_list,$favorite_list,$fresh_things);
        }
        $fresh_things = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$fresh_things);

        return $this->returnResult(200,$fresh_things);
    }

    //获取纯文趣事
    public function getWordThingsList($page,$count,$uid){       
        $funny_list = [];
        $unfunny_list = [];
        $favorite_list = [];
        if($uid > 0){
            $other_list = $this->getOtherThings($uid);
            $funny_list = $other_list['funny_things'];
            $unfunny_list = $other_list['unfunny_things'];            
            $favorite_list = $other_list['favorite_things'];
            // print_r($favorite_list);
        }
        
        $options['table'] = 'things as A';
        $options['join'] = 'user as B on A.user_id = B.user_id'; 
        $options['where'] = array('A.is_approval'=>'?','A.has_img'=>'?');
        $options['param'] =  array(1,0);    
        $options['limit'] = ($page-1)*$count.','.$count; 
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'publish_time desc';
        $word_things = $this->db->select($options);   
       
        if(!empty($word_things)){
            $word_things = $this->handleUser($funny_list,$unfunny_list,$favorite_list,$word_things);
        }
        $word_things = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$word_things);
        return $this->returnResult(200,$word_things);
    }

    //获取带图趣事
    public function getImageThingsList($page,$count,$uid){       
        $funny_list = [];
        $unfunny_list = [];
        $favorite_list = [];
        if($uid > 0){
            $other_list = $this->getOtherThings($uid);
            $funny_list = $other_list['funny_things'];
            $unfunny_list = $other_list['unfunny_things'];            
            $favorite_list = $other_list['favorite_things'];
            // print_r($favorite_list);
        }
        
        //带图 
        $options['table'] = 'things as A';
        $options['join'] = 'user as B on A.user_id = B.user_id';        
        $options['where'] = array('A.is_approval'=>'?','A.has_img'=>'?');
        $options['param'] =  array(1,1);    
        $options['limit'] = ($page-1)*$count.','.$count; 
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'publish_time desc';
        $img_things = $this->db->select($options);   

        if(!empty($img_things)){
            $img_things = $this->handleUser($funny_list,$unfunny_list,$favorite_list,$img_things);
        }
        $img_things = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$img_things);
        return $this->returnResult(200,$img_things);
    }

    //获取用户点过赞\收藏过\踩过的趣事
    public function getOtherThings($uid){
        // print_r($uid);die;

        //该用户点过赞的趣事
        $options1['table'] = 'funny_things';
        $options1['where'] = array('user_id'=>'?');
        $options1['param'] =  array($uid); 
        // print_r($options1);die;

        $funny_list = $this->db->select($options1);
        if(empty($funny_list)){
            $funny_list = array();
        }

        // print_r($funny_list);die;
         //该用户踩过的趣事
        $options2['table'] = 'unfunny_things';
        $options2['where'] = array('user_id'=>'?');
        $options2['param'] =  array($uid); 
        $unfunny_list = $this->db->select($options2);
        // print_r($unfunny_list);die;
        if(empty($unfunny_list)){
            $unfunny_list = array();
        }

        $options3['table'] = 'favorite_things';
        $options3['where'] = array('user_id'=>'?');
        $options3['param'] =  array($uid); 
        $favorite_list = $this->db->select($options3);
        if(empty($favorite_list)){
            $favorite_list = array();
        }
        $otherThing = array(
            'funny_things'=>$funny_list,
            'unfunny_things'=>$unfunny_list,
            'favorite_things'=>$favorite_list
            );
        // print_r($otherThing);die;
        return $otherThing;
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

    //取消收藏
    public function cancelFavorite($things_id,$user_id){
        $options['table'] = 'favorite_things';
        $options['where'] = array('user_id'=>'?','things_id'=>'?');
        $options['param'] = array($user_id,$things_id);
        // print_r($options); 
        $res = $this->db->find($options);
        // print_r($res);
        if(!empty($res)){
            $options1['table'] = 'favorite_things';
            $options1['where'] = array('user_id'=>'?','things_id'=>'?');
            $options1['param'] = array($user_id,$things_id);
            $res1 = $this->db->delete($options1);
            if($res1 !== FALSE){
                $updateSql = 'update things set favorite_num = favorite_num - 1 where things_id =  '.$things_id;
                $info = $this->db->create($updateSql);
                return $this->returnResult(200,$info);
            }else{
                return $this->returnResult(4000);
            }                        
        }else{
            return $this->returnResult(201);            
        }     
    }
    //根据趣事id获取单条趣事
    public function getThingInfo($thing_id,$uid){        
        $options['table'] = 'things as A';
        $options['join'] = 'user as B on A.user_id = B.user_id';
        $options['field'] = 'A.*,B.user_name,B.user_photo,B.register_time';
        $options['where'] = array('A.things_id'=>'?');
        $options['param'] = array($thing_id);
        $thing = $this->db->find($options);

        if(empty($thing)){
            return $this->returnResult(201);
        }

        if(!empty($uid) && $uid > 0){
            $other_thing = $this->getOtherThings($uid);
            $funny_thing = $other_thing['funny_things'];
            $unfunny_thing = $other_thing['unfunny_things'];            
            $favorite_thing = $other_thing['favorite_things'];
        }
        if(!empty($funny_thing)){
            foreach ($funny_thing as $f_key => $f_val) {
                if($thing['things_id'] == $funny_thing[$f_key]['things_id']){
                    $thing['is_praise'] = 1;
                    break;
                }else{
                    $thing['is_praise'] = 0;                     
                }
            }
        }else{
            $thing['is_praise'] = 0;                     
        }
        if(!empty($unfunny_things)){
            foreach ($unfunny_thing as $un_key => $un_val) {
                if($thing['things_id'] == $unfunny_thing[$un_key]['things_id']){
                    $thing['is_tramp'] = 1;
                    break;
                }else{
                    $thing['is_tramp'] = 0;
                }          
            } 
        }else{
            $thing['is_tramp'] = 0;
        }  
        if(!empty($favorite_thing)){            
            foreach ($favorite_thing as $fa_key => $fa_val) {
                if($thing['things_id'] == $favorite_thing[$fa_key]['things_id']){
                    $thing['is_favorite'] = 1;
                    break;
                }else{
                    $thing['is_favorite'] = 0;
                }          
            } 
        }else{
            $thing['is_favorite'] = 0;
        } 
        // // print_r($thing['user_id']);die;
        // $options1['table'] = 'user';
        // $options1['field'] = 'user_name,user_photo';
        // $options1['where'] = array('user_id'=>'?');
        // $options1['param'] = array($thing['user_id']);
        // $user = $this->db->find($options1);
        // if(empty($user)){
        //     return $this->returnResult(201);
        // }
        $thing['publish_time'] = date('Y-m-d H:i:s',$thing['publish_time']);
        // $thing['userInfo'] = $user;
        return $this->returnResult(200,$thing);
    }

    //获取评论列表
    public function getCommentsList($page,$thing_id,$count){
        $options['table'] = 'comment as A';
        $options['join'] = ['user as B on A.user_id = B.user_id'];
        $options['where'] = array('A.things_id'=>'?');
        $options['param'] = array($thing_id);
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'comment_time asc';
        $comments = $this->db->select($options); 

        if(empty($comments)){
            return $this->returnResult(201);
        }
        $cid = [];
        foreach ($comments as $c_key => $c_val) {
            $cid[$c_key] = $c_val['comment_id'];            
        }
        $reply = $this->getReplyList($cid);
        foreach ($comments as $c_key => $c_val) {
            $comments[$c_key]['comment_time'] = date('Y-m-d H:i:s',$comments[$c_key]['comment_time']);
            $comments[$c_key]['reply'] = array();
            if(!empty($reply)){
                foreach ($reply as $r_key => $r_val) {
                    if($c_val['comment_id'] === $r_val['comment_id']){
                        $r_val['reply_time'] = date('Y-m-d H:i:s',$r_val['reply_time']);
                        array_push($comments[$c_key]['reply'],$r_val);
                    }
                } 
            }
                     
        }
        $data = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$comments);
        return $this->returnResult(200,$data);
    }

    //获取回复
    public function getReplyList($cid){
        $options['table'] = 'reply';

        $options['where'] = array('comment_id'=>array('IN',$cid)); 
        $options['param'] = $cid;           

        
        // $options['limit'] = ($page-1)*$count.','.$count;
        // $totalNum = $this->db->count($options);
        // $totalPage = ceil($totalNum/$count);   
        $options['order'] = 'reply_time asc';

        $reply = $this->db->select($options); 
        // $data = array('totalPage'=>$totalPage,'totalNum'=>$totalNum,'page'=>$page,'list'=>$reply);
        return $reply;
    }

    //评论趣事
    public function comment($things_id,$user_id,$content){

        $options1['table'] = 'comment_user';
        $tmpData1 = array('user_id'=>'?','things_id'=>'?');
        $options1['param'] = array($user_id,$things_id);
        $res = $this->db->add($tmpData1,$options1);

        $options1['table'] = 'things';
        $updateSql = 'update things set comment_num = comment_num + 1 where things_id =  '.$things_id;
        $data = $this->db->create($updateSql);

        $options2['table'] = 'comment';
        $tmpData2 = array('user_id'=>'?','things_id'=>'?','content'=>'?','comment_time'=>'?');
        $options2['param'] = array($user_id,$things_id,$content,time());
        $info = $this->db->add($tmpData2,$options2);
        if($res !== FALSE && $info !== FALSE){           
            return $this->returnResult(200,$info);            
        }else{
            return $this->returnResult(4000);            
        }    
    }  

    //回复评论
    public function replyComment($param){
        $options['table'] = 'reply';
        $tmpData = array('reply_user'=>'?','reply_user_name'=>'?','replied_user'=>'?','replied_user_name'=>'?','reply_content'=>'?','comment_id'=>'?','reply_time'=>'?','things_id'=>'?');
        $options['param'] = array($param['reply_user'],$param['reply_user_name'],$param['replied_user'],$param['replied_user_name'],$param['reply_content'],$param['comment_id'],time(),$param['things_id']);
        $res = $this->db->add($tmpData,$options);
        if($res !== FALSE){           
            return $this->returnResult(200,$res);            
        }else{
            return $this->returnResult(4000);            
        }
    }
}
