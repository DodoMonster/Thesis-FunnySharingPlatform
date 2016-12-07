<?php
namespace Log;
class StatModel extends \Core\BaseModels {
    
    //下载记录//1,click;2,complete;3,install;4,enter
    public function statDownload($post){
        if(empty($post['type'])||empty($post['channel_id'])){
            exit;
        }
        $tmpData=[];
        $options['param']=[];
        $tmpData2=[];
        $options2['param']=[];
      
        foreach ($post as $k=>$v){
            $tmpData[$k]='?';
            $options['param'][]=$v;
        }
        $tmpData['ctime']='?';
        $tmpData['cdate']='?';
        $options['param'][]=time();
        $options['param'][]=date("Ymd",time());
        if($post['type']==5){
            $options['table']='log_game_download_pv';
        }else{
            $options['table']='log_game_download_detail';   //游戏下载详细日志表
        }
        $this->db->startTrans();
        $status=$this->db->add($tmpData,$options);

        $options1['table']='log_game_download';
        $options1['where']=['game_id'=>'?','channel_id'=>'?'];
        $options1['param']=[$post['game_id'],$post['channel_id']];
        $status1=$this->db->find($options1);
        switch ($post['type']){
            case 1:
                $tmpData2['num_click_dnld']='?';
                $options2['param'][]=1;
                break;
            case 2:
                $tmpData2['num_com_dnld']='?';
                $options2['param'][]=1;
                break;
            case 3:
                $tmpData2['num_install']='?';
                $options2['param'][]=1;
                if($post['is_first']==1){
                    $tmpData2['num_first_install']='?';
                    $options2['param'][]=1;
                }
                break;
            case 4:
                $tmpData2['num_enter']='?';
                $options2['param'][]=1;
                break;
            case 5:
                $tmpData2['num_click_home']='?';
                $options2['param'][]=1;
                break;
            case 6:
                $tmpData2['num_click_dnld']='?';
                $options2['param'][]=1;
                break;
            default :
                break;
        }
        if($status1){
            //update;
            foreach ($tmpData2 as $k=>$v){
                $tmpData2[$k]=$k.'+ 1';
            }
            $options2['param']=[];
            $options2['table']='log_game_download';
            $options2['where']=['game_id'=>'?','channel_id'=>'?'];
            $options2['param']=[$post['game_id'],$post['channel_id']];
            $status2=$this->db->save($tmpData2,$options2);
        }else{
            //add
            $tmpData2=  array_merge($tmpData2,['game_id'=>'?','channel_id'=>'?']);
            $options2['table']='log_game_download';
            $options2['param']=  array_merge($options2['param'],[$post['game_id'],$post['channel_id']]);
            $status2=$this->db->add($tmpData2,$options2);
        }
        if($status!==FALSE && $status2!==FALSE){
            $this->db->commit();
            return $this->returnResult(200);
        }else{
            $this->db->rollback();
            return $this->returnResult(400);
        }
    }
    
    //数据上报
    public function statReport($post){
        if(!empty($post['idfa']) && $this->isIDFA($post['idfa'])){
            $post['device_id']=$post['idfa'];
        //}elseif(!empty($post['imei'])){
        //    $post['device_id']=$post['imei'];
        }elseif(!empty($post['mac'])){
            $post['device_id']=strtolower($post['mac']);
        }else{
            return $this->returnResult(4300);
        }
        
        $option['table']='market_report';
        $option['where']=['device_id'=>'?','device_type'=>'?'];
        $option['param']=[$post['device_id'],$post['device_type']];
        $status=$this->db->find($option);
        if(!empty($status)){
            return $this->returnResult(201);
        }
        $tmpData1=['game_id'=>'?','channel_id'=>'?','device_type'=>'?','device_id'=>'?','idfa'=>'?','imei'=>'?','mac'=>'?','status'=>'?','ctime'=>'?','cdate'=>'?'];
        $option1['table']='market_report';
        $option1['param']=[$post['game_id'],$post['channel_id'],$post['device_type'],$post['device_id'],$post['idfa'],$post['imei'],$post['mac'],1,time(),date('Y-m-d',time())];
        $status1=$this->db->add($tmpData1,$option1);
        if($status1!==FALSE){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(4000);
        }
    }    
    
    //ios下载去重
    public function statIosDistinct($post){
        $exIdfa=[];
        $result=[];
        $needIdfa=[];
        $needIdfatmpData=[];
        $tmp=  explode(',', $post['idfa']);
        //E0A7947B-9412-41D1-9360-E09296EDB93D       
        if(!empty($tmp)){
            $idfas=[];
            foreach ($tmp as $key=>$value){
                if($this->isIDFA($value)){
                    $idfas[]=$value;
                }
            }
            $options['table']='log_game_download_detail';
            $options['field']='idfa';
            $options['where']=['idfa'=>['IN',$idfas]];
            $options['param']=$idfas;
            $list=$this->db->select($options);
            if(!empty($list)){
                foreach ($list as $k=>$v){
                    $exIdfa[]=$v['idfa'];
                }
            }
            foreach ($idfas as $kt=>$vt){
                if(in_array($vt,$exIdfa)){
                    $result[$vt]=1;
                }else{
                    $result[$vt]=0;
                    $needIdfatmpData[]=['idfa'=>'?'];
                    $needIdfa[]=$vt;
                }
            }
            //没有的加进去
            if(!empty($needIdfa)){
                $options1['table']='log_ios_distinct_idfa';
                $options1['param']=$needIdfa;
                $this->db->addAll($needIdfatmpData,$options1);
            }
        }
        return $result;
    }
    
    //数据上报
    public function statIosReport($post){
        $sourceArray=['adwan','adwanqianka'];
        if(!$this->isIDFA($post['idfa'])||!in_array($post['source'],$sourceArray)){
            return 'faild';
        }
        $options1['table']='log_ios_report_idfa';
        $options1['where']=['idfa'=>'?'];
        $options1['param'][]=$post['idfa'];
        $data=$this->db->find($options1);
        if(!empty($data)){
            return 'faild';
        }
        $tmpData=[];
        $options['param']=[];
        foreach ($post as $k=>$v){
            $tmpData[$k]='?';
            $options['param'][]=$v;
        }
        $tmpData['ctime']='?';
        $options['param'][]=time();
        $tmpData['cdate']='?';
        $options['param'][]=date('Y-m-d',time());
        $options['table']='log_ios_report_idfa';
        $status=$this->db->add($tmpData,$options);
        if($status!==FALSE){
            return 'ok';
        }else{
            return 'failed';
        }
    }
    
    //用户注册日志
    public function getUserRegisterLog($post){
        $uname=isset($post['account'])?$post['account']:(isset($post['uname'])?$post['uname']:'');
        if(strtolower($post['user_agent'])=='ios'){
            $gameId=$post['game_id'].'1';
        }elseif(strtolower($post['user_agent'])=='android'){
            $gameId=$post['game_id'].'2';
        }else{
            $gameId=$post['game_id'].'3';
        }
        $device_id=!empty($post['idfa'])?$post['idfa']:$post['imei'];
        $str="ip=".urlencode($post['ip'])."&gid=".urlencode($gameId)."&cid=".urlencode($post['channel_id'])."&uid=".urlencode($post['uid'])."&username=".urlencode($uname)."&mac=0&packageType=2&device_id=".urlencode($device_id)."&appVersion=".urlencode($post['app_version'])."&sdkVersion=".urlencode($post['sdk_version'])."&suid=".urlencode($post['uid'])."&nickname=0&areaId=0&serverId=-1&os=".urlencode($post['os'])."&osVersion=".urlencode($post['os_version'])."&device=".urlencode($post['device'])."&deviceType=".urlencode($post['devicetype'])."&screen=".urlencode($post['screen'])."&mno=".urlencode($post['mno'])."&nm=".urlencode($post['nm'])."&eventTime=".urlencode(time())."&roleLevel=0&idfv=0"."&agent=".urlencode($post['http_user_agent']);
        $url="http://uniondcs.91sd.com/r.php?".$str;
        \Addons\Grab\GrabUtil::single_grab_getdatatime($url);
    }
        
    //获得用户登录日志
    public function getUserLoginLog(){
        $logs=[];
        $today=date('Y-m-d',  time()-30);
        $id=$this->nosql->get('sync_login_log_id');
        //$id='';
        $options['table']='log_user_login as A';
        $options['join']=['user_device as B on A.device_id=B.device_id'];
        $options['field']='A.id,A.ip,A.device_id,A.uid,A.uname,A.channel_id,A.game_id,A.server_id,B.device_type,B.idfa,B.imei,B.mac,B.os,B.os_version,B.device,B.devicetype,B.screen,B.mno,B.nm,A.login_time,B.app_version,B.sdk_version';
        $options['where']=['A.login_date'=>'?'];
        $options['param']=[$today];
        $options['order']='A.id DESC';
        if($id>0){
            $options['where']= array_merge($options['where'],['A.id'=>['gt','?']]);
            $options['param']= array_merge($options['param'],[$id]);
        }
        $list=$this->db->select($options);
        if(!empty($list)){
            //eventId|ip|did|appVersion|sdkVersion|uid|nickname|channelId|gameId|areaId|serverId|os|osVersion|device|deviceType|screen|mno|nm|eventTime|roleLevel|idfv|ic|referer
            foreach ($list as $k=>$v){
                $v['mac'] = strtolower($v['mac']);
                if($v['device_type']==1 || $v['channel_id']==4){//ios
                    $logs[]=$v['id'].'|'.$v['ip'].'|'.$v['idfa'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'1|'.''.'|'.$v['server_id'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['login_time'].'|'.''.'|'.''.'|'.''.'|'.'';
                }elseif($v['device_type']==2){
                    $logs[]=$v['id'].'|'.$v['ip'].'|'.$v['mac'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'2|'.''.'|'.$v['server_id'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['login_time'].'|'.''.'|'.''.'|'.''.'|'.'';
                }else{
                    $logs[]=$v['id'].'|'.$v['ip'].'|'.$v['mac'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'3|'.''.'|'.$v['server_id'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['login_time'].'|'.''.'|'.''.'|'.''.'|'.'';
                }
            }
            $id=$list[0]['id'];//最末处理的id
            $this->nosql->set('sync_login_log_id',$id,120);
        }
        $fileName='/data/logs/ready/ip185_'.date('YmdHi',time()).'_login.log';
        $this->writeLog($fileName, $logs);         
    }
    
    
    //获得用户支付日志
    public function getUserPayLog($date=''){
        //渠道费率
        $logs=[];
        $payTypeRate=[0,0.994,0.98,0.99,0.6,0.99];
        if(!empty($date)){
            $today=$date;
            $fileday= date('YmdHi',strtotime($date));
        }else{
            $today=date('Y-m-d',  time()-30);
            $fileday=date('YmdHi',time());
        }
        $id=$this->nosql->get('sync_pay_id');
        //$id='';
        $options['table']='log_user_pay as A';
        $options['join']=['user as C on A.uid=C.uid','user_device as B on C.device_id=B.device_id'];
        $options['field']='A.trade_id,C.ip,C.device_id,C.uid,C.uname,C.channel_id,A.game_id,A.game_server,B.device_type,B.idfa,B.imei,B.os,B.os_version,B.device,B.devicetype,B.screen,B.mno,B.nm,B.app_version,B.sdk_version,A.trade_no,A.out_trade_no,A.total_fee,A.pay_type,A.ctime';
        $options['where']=['cdate'=>'?'];
        $options['param']=[$today];
        $options['order']='A.trade_id DESC';
        if($id>0 && $date==''){
            $options['where']= array_merge($options['where'],['A.trade_id'=>['gt','?']]);
            $options['param']= array_merge($options['param'],[$id]);
        }
        $list=$this->db->select($options);
        if(!empty($list)){
            //eventId|ip|did|appVersion|sdkVersion|uid|nickname|channelId|gameId|areaId|serverId|os|osVersion|device|deviceType|screen|mno|nm|eventTime|orderId|orderIdOfGame|goodsId|goodsCount|before|rmbCount|getMoney|after|payType
            foreach ($list as $k=>$v){
                $getMoney=$v['total_fee']*$payTypeRate[$v['pay_type']];
                if($v['device_type']==1 || $v['channel_id']==4 || $v['pay_type']==4){//ios                   
                    $logs[]=$v['trade_id'].'|'.$v['ip'].'|'.$v['idfa'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'1|'.''.'|'.$v['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['ctime'].'|'.$v['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$v['total_fee'].'|'.$getMoney.'|'.''.'|'.$v['pay_type'];
                }elseif($v['device_type']==2){
                    $logs[]=$v['trade_id'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'2|'.''.'|'.$v['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['ctime'].'|'.$v['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$v['total_fee'].'|'.$getMoney.'|'.''.'|'.$v['pay_type'];
                }else{
                    $logs[]=$v['trade_id'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'3|'.''.'|'.$v['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['ctime'].'|'.$v['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$v['total_fee'].'|'.$getMoney.'|'.''.'|'.$v['pay_type'];
                }
            }
            $id=$list[0]['trade_id'];//最末处理的id
            $this->nosql->set('sync_pay_id',$id,120);
        }
        $fileName='/data/logs/ready/ip185_'.$fileday.'_pay.log';     
        $this->writeLog($fileName, $logs);
        
    }
    
    //获取用户支付日志(保存到相应目录)
    public function getUserPayNotifyLog($date=''){
        $logs=[];
        $payTypes=['ALIPAY'=>1,'WECHATPAY'=>2,'YEEPAY'=>3,'APPLEPAY'=>4,'YEEPAYBANK'=>5];
        if(!empty($date)){
            $today=$date;
            $fileday= date('YmdHi',strtotime($date));
        }else{
            $today=date('Y-m-d',  time()-30);
            $fileday=date('YmdHi',time());
        }
        $id=$this->nosql->get('sync_pay_game_id');
        //$id='';
        $startTime=strtotime($today);
        $endTime=$startTime+86400;
        $options['table']='log_notify';
        $options['where']=['notify_time'=>['BETWEEN',['?','?']]];
        $options['param']=[$startTime,$endTime];
        $options['order']='notify_id DESC';
        if($id>0){
            $options['where']= array_merge($options['where'],['notify_id'=>['gt','?']]);
            $options['param']= array_merge($options['param'],[$id]);
        }
        $list=$this->db->select($options);
        if(!empty($list)){
            //eventId|ip|did|appVersion|sdkVersion|uid|nickname|channelId|gameId|areaId|serverId|os|osVersion|device|deviceType|screen|mno|nm|eventTime|orderId|orderIdOfGame|goodsId|goodsCount|before|rmbCount|after|payType|status|url
            foreach ($list as $kt=>$vt){
                $payInfo=  json_decode($vt['notify_data'],true);
                if($payInfo['uid']<0){
                    continue;
                }
                $options1['table']='user as A';
                $options1['join']=['user_device as B on A.device_id=B.device_id'];
                $options1['field']='A.uid,A.uname,A.ip,A.channel_id,B.device_type,B.idfa,B.imei,B.os,B.os_version,B.device,B.devicetype,B.screen,B.mno,B.nm,B.app_version,B.sdk_version';
                $options1['where']=['uid'=>'?'];
                $options1['param']=[$payInfo['uid']];
                $v=$this->db->find($options1);
                if(empty($v)){
                    continue;
                }
                $payType=$payTypes[$payInfo['pay_type']];
                if($v['device_type']==1|| $v['channel_id']==4 ||$payType==4){//ios                   
                    $logs[]=$vt['notify_trade_no'].'|'.$v['ip'].'|'.$v['idfa'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$payInfo['game_id'].'1|'.''.'|'.$payInfo['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$vt['notify_time'].'|'.$payInfo['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$payInfo['total_fee'].'|'.''.'|'.$payType.'|'.'1'.'|'.$vt['notify_url'];
                }elseif($v['device_type']==2){
                    $logs[]=$vt['notify_trade_no'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$payInfo['game_id'].'2|'.''.'|'.$payInfo['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$vt['notify_time'].'|'.$payInfo['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$payInfo['total_fee'].'|'.''.'|'.$payType.'|'.'1'.'|'.$vt['notify_url'];
                }else{
                    $logs[]=$vt['notify_trade_no'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$payInfo['game_id'].'3|'.''.'|'.$payInfo['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$vt['notify_time'].'|'.$payInfo['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$payInfo['total_fee'].'|'.''.'|'.$payType.'|'.'1'.'|'.$vt['notify_url'];
                }
            }
            $id=$list[0]['notify_id'];//最末处理的id
            $this->nosql->set('sync_pay_game_id',$id,120);
        }
        $fileName='/data/logs/ready/ip185_'.$fileday.'_pay_game.log';     
        $this->writeLog($fileName, $logs);
    }
    
    //获得用户支付日志
    public function getUserPayLog1($date=''){
        //渠道费率
        $logs=[];
        $payTypeRate=[0,0.994,0.98,0.99,0.6,0.99]; 
        $today=$date;
        $fileday= date('YmdHi',strtotime($date));
        $id=$this->nosql->get('sync_pay_id');
        //$id='';
        $options['table']='log_user_pay as A';
        $options['join']=['user as C on A.uid=C.uid','user_device as B on C.device_id=B.device_id'];
        $options['field']='A.trade_id,C.ip,C.device_id,C.uid,C.uname,C.channel_id,A.game_id,A.game_server,B.device_type,B.idfa,B.imei,B.os,B.os_version,B.device,B.devicetype,B.screen,B.mno,B.nm,B.app_version,B.sdk_version,A.trade_no,A.out_trade_no,A.total_fee,A.pay_type,A.ctime';
        $options['where']=['cdate'=>['egt','?']];
        $options['param']=[$today];
        $options['order']='A.trade_id DESC';
        $list=$this->db->select($options);
        if(!empty($list)){
            //eventId|ip|did|appVersion|sdkVersion|uid|nickname|channelId|gameId|areaId|serverId|os|osVersion|device|deviceType|screen|mno|nm|eventTime|orderId|orderIdOfGame|goodsId|goodsCount|before|rmbCount|getMoney|after|payType
            foreach ($list as $k=>$v){
                $getMoney=$v['total_fee']*$payTypeRate[$v['pay_type']];
                if($v['device_type']==1 || $v['channel_id']==4 || $v['pay_type']==4){//ios                   
                    $logs[]=$v['trade_id'].'|'.$v['ip'].'|'.$v['idfa'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'1|'.''.'|'.$v['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['ctime'].'|'.$v['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$v['total_fee'].'|'.$getMoney.'|'.''.'|'.$v['pay_type'];
                }elseif($v['device_type']==2){
                    $logs[]=$v['trade_id'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'2|'.''.'|'.$v['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['ctime'].'|'.$v['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$v['total_fee'].'|'.$getMoney.'|'.''.'|'.$v['pay_type'];
                }else{
                    $logs[]=$v['trade_id'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$v['game_id'].'3|'.''.'|'.$v['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$v['ctime'].'|'.$v['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$v['total_fee'].'|'.$getMoney.'|'.''.'|'.$v['pay_type'];
                }
            }
            $id=$list[0]['trade_id'];//最末处理的id
            $this->nosql->set('sync_pay_id',$id,120);
        }
        $fileName='/data/logs/ready/history_'.$fileday.'_pay.log';     
        $this->writeLog($fileName, $logs);     
    }
    
    //获取用户支付日志(保存到相应目录)
    public function getUserPayNotifyLog1($date=''){
        $logs=[];
        $payTypes=['ALIPAY'=>1,'WECHATPAY'=>2,'YEEPAY'=>3,'APPLEPAY'=>4,'YEEPAYBANK'=>5];
        $fileday= date('YmdHi',strtotime('2016-11-22 22:27'));
        $startTime=strtotime('2016-11-22 22:26');
        $endTime=strtotime('2016-11-23');
        $options['table']='log_notify';
        $options['where']=['notify_time'=>['BETWEEN',['?','?']]];
        $options['param']=[$startTime,$endTime];
        $options['order']='notify_id DESC';
        $list=$this->db->select($options);
        if(!empty($list)){
            //eventId|ip|did|appVersion|sdkVersion|uid|nickname|channelId|gameId|areaId|serverId|os|osVersion|device|deviceType|screen|mno|nm|eventTime|orderId|orderIdOfGame|goodsId|goodsCount|before|rmbCount|after|payType|status|url
            foreach ($list as $kt=>$vt){
                $payInfo=  json_decode($vt['notify_data'],true);
                if($payInfo['uid']<0){
                    continue;
                }
                $options1['table']='user as A';
                $options1['join']=['user_device as B on A.device_id=B.device_id'];
                $options1['field']='A.uid,A.uname,A.ip,A.channel_id,B.device_type,B.idfa,B.imei,B.os,B.os_version,B.device,B.devicetype,B.screen,B.mno,B.nm,B.app_version,B.sdk_version';
                $options1['where']=['uid'=>'?'];
                $options1['param']=[$payInfo['uid']];
                $v=$this->db->find($options1);
                if(empty($v)){
                    continue;
                }
                $payType=$payTypes[$payInfo['pay_type']];
                if($v['device_type']==1|| $v['channel_id']==4 ||$payType==4){//ios                   
                    $logs[]=$vt['notify_trade_no'].'|'.$v['ip'].'|'.$v['idfa'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$payInfo['game_id'].'1|'.''.'|'.$payInfo['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$vt['notify_time'].'|'.$payInfo['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$payInfo['total_fee'].'|'.''.'|'.$payType.'|'.'1'.'|'.$vt['notify_url'];
                }elseif($v['device_type']==2){
                    $logs[]=$vt['notify_trade_no'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$payInfo['game_id'].'2|'.''.'|'.$payInfo['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$vt['notify_time'].'|'.$payInfo['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$payInfo['total_fee'].'|'.''.'|'.$payType.'|'.'1'.'|'.$vt['notify_url'];
                }else{
                    $logs[]=$vt['notify_trade_no'].'|'.$v['ip'].'|'.$v['imei'].'|'.$v['app_version'].'|'.$v['sdk_version'].'|'.$v['uid'].'|'.$v['uname'].'|'.$v['channel_id'].'|'.$payInfo['game_id'].'3|'.''.'|'.$payInfo['game_server'].'|'.$v['os'].'|'.$v['os_version'].'|'.$v['device'].'|'.$v['devicetype'].'|'.$v['screen'].'|'.$v['mno'].'|'.$v['nm'].'|'.$vt['notify_time'].'|'.$payInfo['out_trade_no'].'|'.''.'|'.''.'|'.''.'|'.''.'|'.$payInfo['total_fee'].'|'.''.'|'.$payType.'|'.'1'.'|'.$vt['notify_url'];
                }
            }
        }
        echo $fileday;
        var_export($logs);
        $fileName='history_'.$fileday.'_pay_game.log';     
        $this->writeLog($fileName, $logs);
    }
    
    
    //获取用户支付通知日志(保存到相应目录)
    protected function writeLog($fileName,$logs){
        //if(!file_exists($fileName)){
            //mkdir($fileName, 0777, true);
        //}
        $fp = fopen($fileName,"a");
	flock($fp, LOCK_EX) ;
        foreach ($logs as $kt=>$vt){
            fwrite($fp,$logs[$kt]."\n");
        }
        flock($fp, LOCK_UN);
	fclose($fp);
    }
}

