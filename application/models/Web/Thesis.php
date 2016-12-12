<?php
namespace Web;
class ThesisModel extends \Core\BaseModels {

    //登陆
    public function login($param){
        $options['table'] = 'user';
        $options['where'] = array('user_name'=>'?','user_password'=>'?');
        $options['param'] = array($param['user_name'],$param['password']);
        $info = $this->db->find($options);
        // print_r($info);exit;
        if(!empty($info)){
            return $this->returnResult(200,$info);
        }else {
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
