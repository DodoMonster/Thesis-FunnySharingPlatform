<?php
namespace Users;
class UserOauthModel extends \Core\BaseModels {
    
    //登出
    public function logout($mid,$oauthData){
        if($mid<0){
            return $this->returnResult(4300);
        }
        return $this->returnResult(200);
    }
    
    //登录
    public function localLogin($oauthData){
        if(empty($oauthData['account']) || empty($oauthData['password'])){
            return $this->returnResult(4300);
        }
        $isRegAccount=preg_match('/^[A-Za-z0-9_\.]{3,20}$/', $oauthData['account']);
        if(!$isRegAccount){
            return $this->returnResult(4302);
        }
        
        //判断是手机登录还是用户登录
        $isRegCellphone=$this->isRegCellphone($oauthData['account']);
        if($isRegCellphone){//手机登录
            $options['table']='user_cellphone as A';
            $options['join']=['user as B on A.uid=B.uid'];
            $options['where']=['A.cellphone'=>'?'];
            $options['field']='B.uid,B.password,B.uname,A.cellphone';
            $options['param']=[$oauthData['account']];
            $userOauth=$this->db->find($options);
            if(!empty($userOauth)){
                if($userOauth['password']!==md5($oauthData['password'])){                 
                    return $this->returnResult(4303);
                }
                unset($userOauth['password']);
            }else{  
                return $this->returnResult(4302);
            }
        }else{//用户名登录
            $options['table']='user as A';
            $options['join']=['user_cellphone as B on A.uid=B.uid'];
            $options['where']=['A.uname'=>'?'];
            $options['field']='A.uid,A.password,A.uname,B.cellphone';
            $options['param']=[$oauthData['account']];
            $userOauth=$this->db->find($options);
            if(!empty($userOauth)){
                if($userOauth['password']!==md5($oauthData['password'])){
                    return $this->returnResult(4303);
                }
                unset($userOauth['password']);
            }else{
                return $this->returnResult(4302);
            }
        }
        //更新用户设备
        if(!empty($oauthData['device_id'])||!empty($oauthData['idfa'])||!empty($oauthData['imei'])){
            $tmpData1=[];
            $options1['param']=[];
            //更新deviceid
            if(!empty($oauthData['idfa']) && !empty($oauthData['device_id'])){
                $tmpData1=['device_id'=>'?','idfa'=>'?'];
                $options1['param']=[$oauthData['device_id'],$oauthData['idfa']];
            }elseif(!empty($oauthData['idfa']) && empty($oauthData['device_id'])){
                $tmpData1=['device_id'=>'?','idfa'=>'?'];
                $options1['param']=[md5($oauthData['idfa']),$oauthData['idfa']];
            }
            if(!empty($oauthData['imei'])){
                $tmpData1=['device_id'=>'?','imei'=>'?'];
                $options1['param']=[$oauthData['device_id'],$oauthData['imei']];
            }
            $options1['table']='user';
            $options1['where']=['uid'=>'?'];
            $options1['param']=array_merge($options1['param'],[$userOauth['uid']]);
            $this->db->save($tmpData1,$options1);
        }
        //新增设备记录
        $model1=new \Log\LogModel();
        $model1->addUserDevice($oauthData);
        $model1->addUserLoginSuccess($oauthData,$userOauth);
        return $this->returnResult(200,$userOauth);
    }
    
    //用户名注册,reg_type:0其他，1ios自动注册，2ios手动手机注册，3ios手动用户名注册，4安卓自动注册，5安卓手动手机注册，6安卓手动用户名注册，7pc自动注册，8pc手动手机注册，9pc手动用户名注册
    public function registerViaUname($oauthData){
        if(empty($oauthData['account']) || empty($oauthData['password']) || !$this->isRegUname($oauthData['account'])){
            return $this->returnResult(4300);
        }
        //||!$this->isRegPassword($oauthData['password'])
        $isRegCellphone=$this->isRegCellphone($oauthData['account']);
        if($isRegCellphone){
            return $this->returnResult(4008,array('Is Cellphone'));
        }
        $options['table']='user';
        $options['field']='uid';
        $options['where']=array('uname'=>'?');
        $options['param']=array($oauthData['account']);
        $userOauth=$this->db->find($options);
        if(!empty($userOauth)){
            return $this->returnResult(4005);
        }
        $regType=0;
        switch($oauthData['user_agent']){
            case 'ios':
                $regType=3;
                break;
            case 'android':
                $regType=6;
                break;
            case 'pc':
                $regType=9;
                break;
        }
        $tmpData1=array('uname'=>'?','password'=>'?','addtime'=>'?','channel_id'=>'?','ip'=>'?','device_id'=>'?','idfa'=>'?','imei'=>'?','reg_type'=>'?');
        $options1['table']='user';
        $options1['param']=[$oauthData['account'],md5($oauthData['password']),time(),$oauthData['channel_id'],$oauthData['ip'],$oauthData['device_id'],$oauthData['idfa'],$oauthData['imei'],$regType];
        $uid=$this->db->add($tmpData1,$options1);
        if(!empty($uid)){
            $oauthData+=array('uid'=>$uid);
            unset($oauthData['password']);
            //新增设备记录
            $model=new \Log\LogModel();
            $model->addUserDevice($oauthData);
            //注册统计
            $model2=new \Log\StatModel();
            $model2->getUserRegisterLog($oauthData);
            return $this->returnResult(200,$oauthData);
        }else{
            return $this->returnResult(4301);
        }
    }
    
    //手机号码注册,reg_type:0其他，1ios自动注册，2ios手动手机注册，3ios手动用户名注册，4安卓自动注册，5安卓手动手机注册，6安卓手动用户名注册，7pc自动注册，8pc手动手机注册，9pc手动用户名注册
    public function registerViaPhone($oauthData){
        if(empty($oauthData['account']) || !$this->isRegCellphone($oauthData['account']) || empty($oauthData['msg_code'])){
            return $this->returnResult(4300);
        }
         //||!$this->isRegPassword($oauthData['password'])
        if($oauthData['msg_code']){
            $msgCode=$this->nosql->get($oauthData['account']);
            if($msgCode!==$oauthData['msg_code']){
                 $this->returnResult(4001);
            }
        }
        $options['table']='user_cellphone';
        $options['where']=['cellphone'=>'?'];
        $options['param']=[$oauthData['account']];
        $result=$this->db->find($options);
        if(!empty($result)){
            return $this->returnResult(4005);
        }
        $options3['table']='user';
        $options3['where']=['uname'=>'?'];
        $options3['param']=[$oauthData['account']];
        $result3=$this->db->find($options3);
        if(!empty($result3)){
            return $this->returnResult(4005);
        }
        $regType=0;
        switch($oauthData['user_agent']){
            case 'ios':
                $regType=2;
                break;
            case 'android':
                $regType=5;
                break;
            case 'pc':
                $regType=8;
                break;
        }
        $tmpData1=['uname'=>'?','password'=>'?','addtime'=>'?','channel_id'=>'?','ip'=>'?','device_id'=>'?','idfa'=>'?','imei'=>'?','reg_type'=>'?'];
        $options1['table']='user';
        $options1['param']=[$oauthData['account'],md5($oauthData['password']),time(),$oauthData['channel_id'],$oauthData['ip'],$oauthData['device_id'],$oauthData['idfa'],$oauthData['imei'],$regType];
        $this->db->startTrans();
        $uid=$this->db->add($tmpData1,$options1);
        $tmpData2=['cellphone'=>'?','uid'=>'?'];
        $options2['table']='user_cellphone';
        $options2['param']=[$oauthData['account'],$uid];
        $status=$this->db->add($tmpData2,$options2);
        if($uid!=FALSE && $status!=FALSE){
            $oauthData+=['uid'=>$uid];
            $this->db->commit();
            $this->nosql->delete([$oauthData['account']]);
            //新增设备记录
            $model=new \Log\LogModel();
            $model->addUserDevice($oauthData);
            //注册统计
            $model2=new \Log\StatModel();
            $model2->getUserRegisterLog($oauthData);
            unset($oauthData['password']);
            return $this->returnResult(200,$oauthData);
        }else{
            $this->db->rollback();
            return $this->returnResult(4301);
        }
    }
    
    //自动注册,reg_type:0其他，1ios自动注册，2ios手动手机注册，3ios手动用户名注册，4安卓自动注册，5安卓手动手机注册，6安卓手动用户名注册，7pc自动注册，8pc手动手机注册，9pc手动用户名注册
    public function autoRegister($oauthData){
        $uname=bcadd(time(),1000000000).mt_rand(10,99);
        $password=rand(10000001,99999999);
        $regType=0;
        switch($oauthData['user_agent']){
            case 'ios':
                $regType=1;
                break;
            case 'android':
                $regType=4;
                break;
            case 'pc':
                $regType=7;
                break;
        }
        $tmpData=['uname'=>'?','password'=>'?','addtime'=>'?','channel_id'=>'?','ip'=>'?','device_id'=>'?','idfa'=>'?','imei'=>'?','reg_type'=>'?'];
        $options=['table'=>'user','param'=>[$uname,md5($password),time(),$oauthData['channel_id'],$oauthData['ip'],$oauthData['device_id'],$oauthData['idfa'],$oauthData['imei'],$regType]];
        $uid=$this->db->add($tmpData,$options);
        if(!empty($uid)){
            $oauthData+=['uid'=>$uid,'uname'=>$uname,'password'=>$password];
            //新增设备记录
            $model=new \Log\LogModel();
            $model->addUserDevice($oauthData);
            //注册统计
            $model2=new \Log\StatModel();
            $model2->getUserRegisterLog($oauthData);
            return $this->returnResult(200,$oauthData);
        }else{
            return $this->returnResult(4301);
        }
    }
    
    //获得图片验证码
    public function getCaptcha($oauthData){
        if(empty($oauthData['account']) || !$this->isRegCellphone($oauthData['account'])){
            //return $this->returnResult(4300);
        }
        $model=new \Addons\Captcha\CaptchaUtil($oauthData['account']);
        $model->show();
    }
    
    //获得图片验证码
    public function getCaptchaViaRedis($oauthData){
        $model=new \Addons\Captcha\CaptchaUtil($oauthData['account'],['no_session' => true]);
        $data=$model->createCode();
        $this->nosql->set('captcha_'.$oauthData['account'],$data,15*60);
        $model->outputCaptcha();
    }
    
    //检验图片验证码//TODO不用session验证
    public function checkCaptcha($oauthData){
        if(empty($oauthData['account'])){
            return $this->returnResult(4300);
        }
        $cellphone=$oauthData['account'];
        $account=$oauthData['session_id'];
        if(isset($_SESSION['securimage_code_value'][$account])&&($oauthData['captcha_code']==$_SESSION['securimage_code_value'][$account])){
            unset($_SESSION['securimage_code_value'][$account]);
            return $this->sentSmsCode($cellphone);
        }else{
            return $this->returnResult(4002,'Captcha Error');
        }
    }
    
    //发送短信通知
    public function sentSmsNotify($cellphone,$post,$tempId){
        if(empty($cellphone)){//|| !$this->isRegCellphone($cellphone)
            return $this->returnResult(4300);
        }
        $datas=$post;
        $model=new \Addons\Sms\SmsUtil();
        $result=$model->sendTemplateSMS($cellphone,$datas,$tempId);
        if($result == NULL ) {
            return $this->returnResult(4000);
        }
        if($result->statusCode!=0) {
            return $this->returnResult(4007,[$result->statusCode,$result->statusMsg]);
        }else{
            $smsmessage = $result->TemplateSMS;
            return $this->returnResult(200,["dateCreated"=>$smsmessage->dateCreated,"smsMessageSid"=>$smsmessage->smsMessageSid]);
        }
    }
    
    //发送短信验证码(后缀,tempid)
    public function sentSmsCodefm($cellphone,$tempId,$ex='_fm'){
        if(empty($cellphone)|| !$this->isRegCellphone($cellphone)){
            return $this->returnResult(4300);
        }
        $code=rand(100,999).rand(100, 999);
        $expireTime=15*60;
        $datas=array($code,15);//15分钟有效
        $this->nosql->set($cellphone.$ex,$code,$expireTime);
        $model=new \Addons\Sms\SmsUtil();
        $result=$model->sendTemplateSMS($cellphone,$datas,$tempId);
        if($result == NULL ) {
            return $this->returnResult(4000);
        }
        if($result->statusCode!=0) {
            return $this->returnResult(4007,['发送过于频繁,或超过次数限制']);
        }else{
            $smsmessage = $result->TemplateSMS;
            return $this->returnResult(200,["dateCreated"=>$smsmessage->dateCreated,"smsMessageSid"=>$smsmessage->smsMessageSid]);
        }
    }
    
    //发送短信验证码
    public function sentSmsCode($cellphone){
        if(empty($cellphone)){
            return $this->returnResult(4300);
        }
        $code=rand(100,999).rand(100, 999);
        $expireTime=15*60;
        $datas=array($code,15);//15分钟有效
        //$tempId=81542;        
        $tempId=107504;
        $this->nosql->set($cellphone,$code,$expireTime);
        $model=new \Addons\Sms\SmsUtil();
        $result=$model->sendTemplateSMS($cellphone,$datas,$tempId);
        if($result == NULL ) {
            return $this->returnResult(4000);
        }
        if($result->statusCode!=0) {
            //return $this->returnResult(4007,array($result->statusCode,$result->statusMsg));
            return $this->returnResult(4007,['发送过于频繁,或超过次数限制']);
        }else{
            $smsmessage = $result->TemplateSMS;
            return $this->returnResult(200,["dateCreated"=>$smsmessage->dateCreated,"smsMessageSid"=>$smsmessage->smsMessageSid]);
        }
    }
    
    //手机是否注册
    public function isRegisterCellphone($oauthData){
        if(empty($oauthData['account']) || !$this->isRegCellphone($oauthData['account'])){
            return $this->returnResult(4300);
        }
        $options=array('table'=>'user_cellphone','field'=>'cellphone','where'=>array('cellphone'=>'?'),'param'=>array($oauthData['account']));
        $status=$this->db->find($options);
        if(!empty($status)){
            return $this->returnResult(4005);
        }else{
            return $this->returnResult(200,'Not Registered');
        }
    }
    
    //忘记密码
    public function forgetPassword($oauthData){
         if(empty($oauthData['account']) || !$this->isRegCellphone($oauthData['account']) || empty($oauthData['msg_code'])){
            return $this->returnResult(4300);
        }
        if($oauthData['msg_code']){
            $msgCode=$this->nosql->get($oauthData['account']);
            if($msgCode!==$oauthData['msg_code']){
                 $this->returnResult(4001);
            }
        }
        $options['table']='user_cellphone';
        $options['where']=array('cellphone'=>'?');
        $options['param']=array($oauthData['account']);
        $result=$this->db->find($options);
        if(empty($result)){
            return $this->returnResult(4302);
        }
        $tmpData3=['password'=>'?'];
        $options3['table']='user';
        $options3['where']=['uid'=>'?'];
        $options3['param']=[md5($oauthData['password']),$result['uid']];
        $result3=$this->db->save($tmpData3,$options3);
        if($result3!==FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }
    
    
    //第三方平台授权绑定自己平台账号
    public function oauthBindAccount($oauthData,$game){
        if(empty($oauthData['oauth_uid'])){
            return $this->returnResult(4300);
        }
        $options['table']='user_oauth';
        $options['where']=array('oauth_uid'=>'?','channel_id'=>'?');
        $options['param']=array($oauthData['oauth_uid'],$oauthData['channel_id']);
        $result=$this->db->find($options);
        if(!empty($result)){
            return $this->returnResult(4003);
        }
        
        $options2['table']=$game['game_table_name'];
        $options2['where']=array('game_uid'=>'?');
        $options2['param']=array($oauthData['game_uid']);
        $result2=$this->db->find($options2);
        if(!empty($result2)){
            return $this->returnResult(4011);
        }
        
        $uname=time()+mt_rand(10000000,99999999)+1000000000;
        $password=rand(10000001,99999999);
        $tmpData1=array('uname'=>'?','password'=>'?','addtime'=>'?','channel_id'=>'?','ip'=>'?','device_id'=>'?');
        $options1=array('table'=>'user','param'=>array($uname,md5($password),time(),$oauthData['channel_id'],$oauthData['ip'],$oauthData['device_id']));
        $this->db->startTrans();
        $uid=$this->db->add($tmpData1,$options1);
     
        $tmpData4=array('uid'=>'?','oauth_uid'=>'?','channel_id'=>'?','token'=>'?','user_token'=>'?','state'=>'?','ctime'=>'?');
        $options4['table']='user_oauth';
        $options4['param']=array($uid,$oauthData['oauth_uid'],$oauthData['channel_id'],$oauthData['token'],$oauthData['user_token'],0,time());
        $status4=$this->db->add($tmpData4,$options4);
        
        $tmpData3=array('uid'=>'?','channel_id'=>'?','device_id'=>'?','game_id'=>'?','game_uid'=>'?','game_uname'=>'?','game_server'=>'?','addtime'=>'?');
        $options3['table']=$game['game_table_name'];
        $options3['param']=array($uid,$oauthData['channel_id'],$oauthData['device_id'],$game['game_id'],$oauthData['game_uid'],$oauthData['game_uname'],$oauthData['game_server'],time());
        $status3=$this->db->add($tmpData3,$options3);
        
        if($uid!==FALSE && $status3!==FALSE && $status4!==FALSE){
            $this->db->commit();
            $oauthData+=array('uid'=>$uid,'uname'=>$uname,'password'=>$password);
            //新增设备记录
            $model=new \Log\LogModel();
            $model->addUserDevice($oauthData);
            return $this->returnResult(200,$oauthData);
        }else{
            $this->db->rollback();
            return $this->returnResult(4012);
        }    
    }
    
    //获取第三方平台账号信息
    public function getOauthUserInfo($oauthData,$game){
        $options['table']='user_oauth as A';
        $options['join']=array('user as B on A.uid=B.uid',$game['game_table_name'].' as C on A.uid=C.uid');
        $options['field']='A.uid,A.oauth_uid,A.channel_id,A.token,A.user_token,B.uid,B.uname,C.game_uid,C.game_uname,C.game_server';
        $options['where']=array('oauth_uid'=>'?','A.channel_id'=>'?','A.token'=>'?','A.user_token'=>'?');
        $options['param']=array($oauthData['oauth_uid'],$oauthData['channel_id'],$oauthData['token'],$oauthData['user_token']);
        $result=$this->db->find($options);
        if(isset($result['uid']) && !empty($result['uid'])){
            //登录日志记录
            $model=new \Log\LogModel();
            $model->addUserLogin($result['uid'], $result['uname'], $game['game_id'],$result['game_uid'], $oauthData);
            return $this->returnResult(200,$result);
        }else{
            return $this->returnResult(4013);
        }
    }
            
    //ios更新包
    public function getIosVersion($post){
        $options['table']='sys_ios_update';
        $options['where']=['version_code'=>"?"];
        $options['param']=[$post['version_code']];
        $result=$this->db->find($options);
        return $this->returnResult(200,$result);
    }

    // 强行删除用户的手机绑定
    public function deleteBindCellphone($oauthData){
        $options['table'] = 'user_cellphone';
        $options['where'] = array('cellphone'=>'?');
        $options['param'] = array($oauthData['account']);
        $result = $this->db->find($options);
        if(empty($result)){
            return $this->returnResult(4201);
        }
        $options1['table'] = 'user';
        $options1['where'] = array('uid'=>'?');
        $options1['param'] = array($result['uid']);
        $result1 = $this->db->find($options1);
        if(empty($result1)){
            return $this->returnResult(4302);
        }
        if($result['cellphone']==$result1['uname']){
            return $this->returnResult(4202);
        }
        $options2['table'] = 'member_phone_bind';
        $options2['where'] = array('phone'=>'?');
        $options2['param'] = array($oauthData['account']);
        $result2 = $this->db->find($options2);
        if(!empty($result2)){       // member_phone_bind表有数据，删
            $this->db->startTrans();
            $status = $this->db->delete($options);
            $status2 = $this->db->delete($options2);
            if($status2!==FALSE && $status!==FALSE){
                $this->db->commit();
                return $this->returnResult(200,$oauthData);
            }else {
                $this->db->rollback();
                return $this->returnResult(4000);
            }
        }else {
            $status = $this->db->delete($options);
            if($status!==FALSE){
                return $this->returnResult(200,$oauthData);
            }else {
                return $this->returnResult(4000);
            }
        }
    }
}
