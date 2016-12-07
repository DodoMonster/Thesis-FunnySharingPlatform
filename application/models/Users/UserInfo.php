<?php
namespace Users;
class UserInfoModel extends \Core\BaseModels {
    
    //获得游戏基本信息[Notify专用]
    public function getGameInfo($gameId){
        $options['table']='game as A';
        $options['join']=['oauth2_clients as B on A.client_id=B.client_id'];
        $options['field']='game_id,game_name,game_table_name,pay_redirect_uri,B.client_id,B.client_secret,B.redirect_uri';
        $options['where']=['game_id'=>'?'];
        $options['param']=[$gameId];
        $game=$this->db->find($options);
        return !empty($game)?$game:array();
    }
    
    //获得用户信息//手机号//游戏充值
    public function getUserIdViaUname($uname,$gameId){
        $options1['table']='game';
        $options1['field']='game_table_name';
        $options1['where']=['game_id'=>'?'];
        $options1['param']=[$gameId];
        $table=$this->db->find($options1);
        if(empty($table)){
            return $this->returnResult(201);
        }
        $gameUserTable=$table['game_table_name'];
        $options['table']='user as A';
        $options['field']='A.uid,B.game_uid';
        $options['join']=["$gameUserTable as B on A.uid=B.uid"];
        $options['where']=['A.uname'=>'?'];
        $options['param']=[$uname];
        $userInfo=$this->db->find($options);
        if(empty($userInfo)){
            $gameUserTable=$table['game_table_name'];
            $options2['table']='user_cellphone as A';
            $options2['field']='A.uid,B.game_uid';
            $options2['join']=["$gameUserTable as B on A.uid=B.uid"];
            $options2['where']=['A.cellphone'=>'?'];
            $options2['param']=[$uname];
            $userInfo=$this->db->find($options2);
        }
        return $this->returnResult(200,$userInfo);
    }
     
    //获得用户信息
    public function getUserInfo($uid,$game,$oauthData){
        $gameUserTable=$game['game_table_name'];
        $options['table']='user as A';
        $options['field']='A.uid,A.uname,C.cellphone,B.game_id,B.game_uid,B.game_uname,B.game_server';
        $options['join']=["$gameUserTable as B on A.uid=B.uid","user_cellphone as C on A.uid=C.uid"];
        $options['where']=['A.uid'=>'?'];
        $options['param']=[$uid];
        $userInfo=$this->db->find($options);
        if(!empty($userInfo)){           
            unset($game['game_table_name']);
            $userInfo['game_info']=$game;
            $userInfo['oauth_token']=$oauthData['oauth_token'];
            $userInfo['verify_token']=base64_encode($uid.'_'.$userInfo['game_uid'].'_'.$oauthData['oauth_token'].'_'.time());
            //登录日志记录
            $model=new \Log\LogModel();
            $model->addUserLogin($uid, $userInfo['uname'], $game['game_id'],$userInfo['game_uid'], $oauthData);
            if($oauthData['uid']!==''&&$oauthData['uname']!==''){
                if($uid!=$oauthData['uid']){
                    return $this->returnResult(4304);
                }
            }
            return $this->returnResult(200,$userInfo);
        }else{
            return $this->returnResult(4304);
        }
    }
    
    //验证用户信息
    public function verifyUserInfo($oauthData,$game){
        if(empty($oauthData['verify_token'])){
            return $this->returnResult(4000);
        }
        $tmp=explode('_', base64_decode($oauthData['verify_token']));
        $oauthData['uid']=isset($tmp[0])?$tmp[0]:'';
        $oauthData['game_uid']=isset($tmp[1])?$tmp[1]:'';
        $time=isset($tmp[3])?$tmp[3]:'';
        $nowtime=time();
        if(!isset($tmp[2]) || ($tmp[2]!==$oauthData['oauth_token']) || empty($oauthData['uid']) || empty($oauthData['game_uid'])|| empty($time) || $time+180<$nowtime){
            return $this->returnResult(4000,'Not bind GameUid Or Time Expire');
        }                
        $gameUserTable=$game['game_table_name'];
        $options['table']='user as A';
        $options['field']='A.uid,A.uname,C.cellphone,B.game_id,B.game_uid';
        $options['join']=array("$gameUserTable as B on A.uid=B.uid","user_cellphone as C on A.uid=C.uid");
        $options['where']=array('A.uid'=>'?','B.game_uid'=>'?');
        $options['param']=array($oauthData['uid'],$oauthData['game_uid']);
        $userInfo=$this->db->find($options);
        if(!empty($userInfo)){
            return $this->returnResult(200,$userInfo);
        }else{
            return $this->returnResult(4000,'Verify Error');
        }
    }
    
    //重置密码
    public function resetPassword($uid,$password,$newPassword){
        //判断旧密码是否正确
        $options1=array('table'=>'user','field'=>'password','where'=>array('uid'=>'?'),'param'=>array($uid));
        $status1=$this->db->find($options1);
        if(isset($status1['password']) && $status1['password']!=md5($password)){
            return $this->returnResult(4303);
        }

        $tmpData=array('password'=>'?');
        $options=array('table'=>'user','where'=>array('uid'=>'?','password'=>'?'),'param'=>array(md5($newPassword),$uid,md5($password)));
        $status=$this->db->save($tmpData,$options);
        if($status!==FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }
    
    //绑定手机
    public function bindCellphone($uid,$oauthData){
        if(empty($oauthData['account']) || !$this->isRegCellphone($oauthData['account']) || empty($oauthData['msg_code'])){
            return $this->returnResult(4300);
        }
        if($oauthData['msg_code']){
            $msgCode=$this->nosql->get($oauthData['account']);
            if($msgCode!==$oauthData['msg_code']){
               return $this->returnResult(4001);
            }
        }
        //手机号是否绑定//用户是否绑定手机
        $options['table']='user_cellphone';
        $options['where']=array('_logic'=>'OR','cellphone'=>'?','uid'=>'?');
        $options['param']=array($oauthData['account'],$uid);
        $result=$this->db->find($options);
        if(!empty($result)){
            return $this->returnResult(4305);
        }       
        $tmpData1=array('cellphone'=>'?','uid'=>'?');
        $options1['table']='user_cellphone';
        $options1['param']=array($oauthData['account'],$uid);
        $status=$this->db->add($tmpData1,$options1);
        if($status!==FALSE){
            $this->nosql->delete(array($oauthData['account']));
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4306);
        }
    }
    
    //解除绑定手机
    public function unbindCellphone($uid,$oauthData){
        if(empty($oauthData['account']) || !$this->isRegCellphone($oauthData['account']) || empty($oauthData['msg_code'])){
            return $this->returnResult(4300);
        }
        if($oauthData['msg_code']){
            $msgCode=$this->nosql->get($oauthData['account']);
            if($msgCode!==$oauthData['msg_code']){
               return $this->returnResult(4001);
            }
        }
        //手机号是否绑定//用户是否绑定手机
        $options['table']='user_cellphone as A';
        $options['join']='user as B on A.uid=B.uid';
        $options['field']="A.*,B.uname";
        $options['where']=array('A.cellphone'=>'?','A.uid'=>'?');
        $options['param']=array($oauthData['account'],$uid);
        $result=$this->db->find($options);
        if(empty($result)){
            return $this->returnResult(4302);
        }
        if($result['cellphone']==$result['uname']){
            return $this->returnResult(4009);
        }
        $options1['table']='user_cellphone';
        $options1['where']=array('cellphone'=>'?','uid'=>'?');
        $options1['param']=array($oauthData['account'],$uid);
        $status=$this->db->delete($options1);
        if($status!==FALSE){
            $this->nosql->delete(array($oauthData['account']));
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4307);
        }
    }
    
    //绑定游戏账号
    public function bindGameAccount($uid,$oauthData,$game){
        $options['table']=$game['game_table_name'];
        $options['where']=array('_logic'=>'OR','uid'=>'?','game_uid'=>'?');
        $options['param']=array($uid,$oauthData['game_uid']);
        $result=$this->db->find($options);
        if(!empty($result)){
            return $this->returnResult(4011);
        }
        $tmpData1=array('uid'=>'?','channel_id'=>'?','device_id'=>'?','game_id'=>'?','game_uid'=>'?','game_uname'=>'?','game_server'=>'?','addtime'=>'?','adddate'=>'?');
        $options1['table']=$game['game_table_name'];
        $options1['param']=array($uid,$oauthData['channel_id'],$oauthData['device_id'],$game['game_id'],$oauthData['game_uid'],$oauthData['game_uname'],$oauthData['game_server'],time(),date('Y-m-d',time()));
        $status=$this->db->add($tmpData1,$options1);
        if($status!==FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4010);
        }
    }
    
    //游戏创角
    public function createGameRole($uid,$gameInfo,$oauthData){
        $platform=['ios','android','pc','web'];
        if($uid!=$oauthData['uid'] || !in_array($oauthData['platform'],$platform)){
            return $this->returnResult(4300);
        }
        $options['table']=$gameInfo['game_table_name'].'_role';
        $options['where']=['uid'=>'?','game_uid'=>'?','role_id'=>'?'];
        $options['param']=[$uid,$oauthData['game_uid'],$oauthData['role_id']];
        $status=$this->db->find($options);
        if(!empty($status)){
            return $this->returnResult(4000);
        }
        $tmpData1=['uid'=>'?','game_uid'=>'?','platform'=>'?','game_id'=>'?','server_id'=>'?','server_name'=>'?','role_id'=>'?','role_name'=>'?','ctime'=>'?','cdate'=>'?'];
        $options1['table']=$gameInfo['game_table_name'].'_role';
        $options1['param']=[$uid,$oauthData['game_uid'],$oauthData['platform'],$gameInfo['game_id'],$oauthData['server_id'],$oauthData['server_name'],$oauthData['role_id'],$oauthData['role_name'],time(),date("Y-m-d",time())];
        $status1=$this->db->add($tmpData1,$options1);
        if($status1!==FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }    
    
    //生成短链接
    public function addPromoteLink($oauthData){
        if(!in_array($oauthData['gameflag'],array('dzz')) || empty($oauthData['linkno'])){
            return $this->returnResult(4300);
        }
        $options1['table']='game_user_'.$oauthData['gameflag'].'_link';
        $options1['where']=['linkno'=>'?'];
        $options1['param']=[$oauthData['linkno']];
        $status1=$this->db->find($options1);
        if($status1){
            $tmp=explode('_', $oauthData['linkno']);
            $number=intval($tmp[1]);
            $mkkey=$this->base62($number);
            $tmpData['mkkey']='?';
            $options['param'][]=$mkkey;
            $tmpData['os_id']='?';
            switch ($oauthData['platformid']){
                case 'cy_ad':
                    $options['param'][]=0;
                    break;
                case 'cy_ios':
                    $options['param'][]=2;
                    break;
                case 'cy_msdk':
                    $options['param'][]=1;
                    break;
                case  'qsdk_ad':
                    $options['param'][]=3;
                    break;
                case  'ty_ad':
                    $options['param'][]=4;
                    break;
                case  'ty_ios':
                    $options['param'][]=5;
                    break;
                default:
                    $options['param'][]=5;
                    break;
            }   
            foreach ($oauthData as $k=>$v){
                if($k!='linkno'){
                    $tmpData[$k]='?';
                    $options['param'][]=$v;
                }
            }
            $tmpData['ctime']='?';
            $options['param'][]=time();
            $options['where']=['linkno'=>'?'];
            $options['param'][]=$oauthData['linkno'];
            $options['table']='game_user_'.$oauthData['gameflag'].'_link';
            $status2=$this->db->save($tmpData,$options);
            if($status2!==FALSE){
                $oauthData['mkkey']=$mkkey;
                return $this->returnResult(200,$oauthData);
            }else{
                return $this->returnResult(4000);
            }
        }
        $tmp=explode('_', $oauthData['linkno']);
        $number=intval($tmp[1]);
        $tmpData['mkkey']='?';
        $options['param'][]=$this->base62($number);
        $tmpData['os_id']='?';
        switch ($oauthData['platformid']){
            case 'cy_ad':
                $options['param'][]=0;
                break;
            case 'cy_ios':
                $options['param'][]=2;
                break;
            case 'cy_msdk':
                $options['param'][]=1;
                break;
            case  'qsdk_ad':
                $options['param'][]=3;
                break;
            case  'ty_ad':
                $options['param'][]=4;
                break;
            case  'ty_ios':
                $options['param'][]=5;
                break;
            default:
                $options['param'][]=5;
                break;
        }   
        foreach ($oauthData as $k=>$v){
            $tmpData[$k]='?';
            $options['param'][]=$v;
        }
        $tmpData['ctime']='?';
        $options['param'][]=time();
        $options['table']='game_user_'.$oauthData['gameflag'].'_link';
        $status=$this->db->add($tmpData,$options);
        if($status){
            $oauthData['mkkey']=$this->base62($number);
            return $this->returnResult(200,$oauthData);
        }else{
            return $this->returnResult(4000);
        }
    }
    
}

