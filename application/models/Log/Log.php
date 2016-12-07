<?php
namespace Log;
class LogModel extends \Core\BaseModels {
    
    //添加用户支付日志
    public function addLogUserPay($payType,$post){
       
        if($payType==1){           
            $this->addLogUserPayAli($payType,$post);
        }elseif($payType==2){                      
            $this->addLogUserPayWetchat($payType,$post);
        }elseif($payType==3){           
            $this->addLogUserPayYee($payType,$post);
        }elseif($payType==4){           
            $this->addLogUserPayApple($payType,$post);
        }elseif($payType==5){           
            $this->addLogUserPayYeebank($payType,$post);
        }elseif($payType==6){          
            $this->addLogUserQuicksdk($payType,$post);
        }
    }
    
    //添加支付宝请求日志
    public function addLogUserPayAli($payType,$post){
        if(isset($post['trade_status']) && $post['trade_status']==='TRADE_SUCCESS'){
            //查找是否存在
            $options1['table']= 'log_user_pay';
            $options1['where']= array('trade_no'=>'?','pay_type'=>'?');
            $options1['param']= array($post['trade_no'],$payType);
            //$options1['lock']= true;
            $this->db->startTrans();
            $tradeInfo=$this->db->find($options1);
            if(!empty($tradeInfo)){
                if($tradeInfo['trade_status']!=$post['trade_status']){
                    $tmpData2=array('trade_status'=>'?');
                    $options2['table']='log_user_pay';
                    $options2['where']= array('trade_no'=>'?','pay_type'=>'?');
                    $options2['param']= array($post['trade_status'],$post['trade_no'],$payType);
                    $status=$this->db->save($tmpData2,$options2);
                    if($status!==FALSE){
                        $this->db->commit();
                    }else{
                        $this->db->rollback();
                    }                        
                }else{
                    $this->db->commit();
                }
            }else{
                //time+uid+game_id+game_uid+server_id//
                $tmp1=explode('@#', $post['extra_common_param']);
                $extraParam=isset($tmp1[1])?$tmp1[1]:'';
                $tmp=explode('_', $tmp1[0]);
                $serverid=isset($tmp[3])?$tmp[3]:'';
                $tmpData2=array('uid'=>'?','trade_no'=>'?','pay_type'=>'?','out_trade_no'=>'?','trade_status'=>'?','buyer_id'=>'?','price'=>'?','total_fee'=>'?','trade_time'=>'?','game_id'=>'?','game_uid'=>'?','game_server'=>'?','extra_param'=>'?','ctime'=>'?','cdate'=>'?');
                $options2['param']=array($tmp[0],$post['trade_no'],$payType,$post['out_trade_no'],$post['trade_status'],$post['buyer_id'],$post['price'],$post['total_fee'],strtotime($post['gmt_create']),$tmp[1],$tmp[2],$serverid,$extraParam,time(),date('Y-m-d',time()));
                $options2['table']='log_user_pay';
                $status=$this->db->add($tmpData2,$options2);
                $this->db->commit();
            }
        }
        
        $options3['table']='log_user_pay_ali';
        $options3['where']=array('trade_no'=>'?','trade_status'=>'?');
        $options3['param']=array($post['trade_no'],$post['trade_status']);
        $result=$this->db->find($options3);
        if(!empty($result)){
            return;
        }
        $tmpData4=array();
        $options4['param']=array();
        foreach ($post as $k=>$v){
            $tmpData4[$k]='?';
            $options4['param'][]=$v;
        }
        $options4['table']='log_user_pay_ali';
        $status=$this->db->add($tmpData4,$options4);
    }
    
    //添加微信支付请求日志
    public function addLogUserPayWetchat($payType,$post){
        if(isset($post['result_code']) && $post['result_code']=='SUCCESS' && isset($post['return_code']) && $post['return_code']=='SUCCESS'){
            $options1['table']= 'log_user_pay';
            $options1['where']= array('trade_no'=>'?','pay_type'=>'?');
            $options1['param']= array($post['transaction_id'],$payType);
            $tradeInfo=$this->db->find($options1);
            if(!empty($tradeInfo)){
                return;
            }
            //$this->db->startTrans();
            //0.uid,1.game_id,2.game_uid
            //$tmp=explode('_', $post['attach']);
            $tmp1=explode('@#', $post['attach']);
            $extraParam=isset($tmp1[1])?$tmp1[1]:'';
            $tmp=explode('_',$tmp1[0]);
            $serverid=isset($tmp[3])?$tmp[3]:'';
            $tmpData2=array('uid'=>'?','trade_no'=>'?','pay_type'=>'?','out_trade_no'=>'?','trade_status'=>'?','buyer_id'=>'?','price'=>'?','total_fee'=>'?','trade_time'=>'?','game_id'=>'?','game_uid'=>'?','game_server'=>'?','extra_param'=>'?','ctime'=>'?','cdate'=>'?');
            $options2['param']=array($tmp[0],$post['transaction_id'],$payType,$post['out_trade_no'],'SUCCESS',0,$post['cash_fee']/100,$post['total_fee']/100,strtotime($post['time_end']),$tmp[1],$tmp[2],$serverid,$extraParam,time(),date('Y-m-d',time()));
            $options2['table']='log_user_pay';
            $status=$this->db->add($tmpData2,$options2);
            //$this->db->commit();
        }
        if(isset($post['device_info'])&& $post['device_info']=='WEB'){
            unset($post['device_info']);
        }
        $options3['table']='log_user_pay_wechat';
        $options3['where']=array('transaction_id'=>'?');
        $options3['param']=array($post['transaction_id']);
        $result=$this->db->find($options3);
        if(!empty($result)){
            return;
        }
        $tmpData4=array();
        $options4['param']=array();
        foreach ($post as $k=>$v){
            $tmpData4[$k]='?';
            $options4['param'][]=$v;
        }
        $options4['table']='log_user_pay_wechat';
        $status=$this->db->add($tmpData4,$options4);
    }
    
    //添加易宝支付请求日志
    public function addLogUserPayYee($payType,$post){
        if(isset($post['r1_Code']) && $post['r1_Code']==1){
            $options1['table']= 'log_user_pay';
            $options1['where']= array('trade_no'=>'?','pay_type'=>'?');
            $options1['param']= array($post['p2_Order'],$payType);
            //$this->db->startTrans();
            $tradeInfo=$this->db->find($options1);
            if(empty($tradeInfo)){
                //0.uid,1.game_id,2.game_uid
                //$tmp1=explode('@#', $post['p9_MP']);
                $tmp1=explode('_yeepay_', $post['p9_MP']);
                $extraParam=isset($tmp1[1])?$tmp1[1]:'';
                $tmp=explode('_',$tmp1[0]);
                $serverid=isset($tmp[3])?$tmp[3]:'';
                $tmpData2=array('uid'=>'?','trade_no'=>'?','pay_type'=>'?','out_trade_no'=>'?','trade_status'=>'?','buyer_id'=>'?','price'=>'?','total_fee'=>'?','trade_time'=>'?','game_id'=>'?','game_uid'=>'?','game_server'=>'?','extra_param'=>'?','ctime'=>'?','cdate'=>'?');
                $options2['param']=array($tmp[0],$post['r2_TrxId'],$payType,$post['p2_Order'],'SUCCESS',0,$post['p3_Amt'],$post['p3_Amt'],time(),$tmp[1],$tmp[2],$serverid,$extraParam,time(),date('Y-m-d',time()));
                $options2['table']='log_user_pay';
                $status=$this->db->add($tmpData2,$options2);
            }
            //$this->db->commit();
        }
        
        $options3['table']='log_user_pay_yee';
        $options3['where']=array('order'=>'?','code'=>'?');
        $options3['param']=array($post['p2_Order'],$post['r1_Code']);
        $result=$this->db->find($options3);
        if(!empty($result)){
            return;
        }
        $tmpData4=array('trx_id'=>'?','cmd'=>'?','code'=>'?','mer_id'=>'?','order'=>'?','amt'=>'?','frp_id'=>'?','card_no'=>'?','confirm_amount'=>'?','real_amount'=>'?','card_status'=>'?','mp'=>'?','balance_amt'=>'?','balance_act'=>'?','hmac'=>'?','ctime'=>'?');
        $options4['param']=array($post['r2_TrxId'],$post['r0_Cmd'],$post['r1_Code'],$post['p1_MerId'],$post['p2_Order'],$post['p3_Amt'],$post['p4_FrpId'],$post['p5_CardNo'],$post['p6_confirmAmount'],$post['p7_realAmount'],$post['p8_cardStatus'],$post['p9_MP'],$post['pb_BalanceAmt'],$post['pc_BalanceAct'],$post['hmac'],time());
        $options4['table']='log_user_pay_yee';
        $status=$this->db->add($tmpData4,$options4);       
    }
    
    //添加apple支付信息
    public function addLogUserPayApple($payType,$post){
        $options1['table']= 'log_user_pay';
        $options1['where']= ['trade_no'=>'?','pay_type'=>'?'];
        $options1['param']= [$post['trade_no'],$payType];
        $tradeInfo=$this->db->find($options1);
        if($tradeInfo){
           return; 
        }
        $this->db->startTrans();
        $tmpData2=['uid'=>'?','trade_no'=>'?','pay_type'=>'?','out_trade_no'=>'?','trade_status'=>'?','buyer_id'=>'?','price'=>'?','total_fee'=>'?','trade_time'=>'?','game_id'=>'?','game_uid'=>'?','game_server'=>'?','extra_param'=>'?','ctime'=>'?','cdate'=>'?'];
        $options2['param']=[$post['uid'],$post['trade_no'],$payType,$post['out_trade_no'],'SUCCESS',0,$post['total_fee'],$post['total_fee'],time(),$post['game_id'],$post['game_uid'],$post['game_server'],$post['extra_common_param'],time(),date('Y-m-d',time())];
        $options2['table']='log_user_pay';
        $status2=$this->db->add($tmpData2,$options2);        
        $tmpData4=['receipt_type'=>'?','bid'=>'?','bvrs'=>'?','unique_vendor_identifier'=>'?','item_id'=>'?','quantity'=>'?','product_id'=>'?','transaction_id'=>'?','original_transaction_id'=>'?','purchase_date'=>'?','purchase_date_ms'=>'?','purchase_date_pst'=>'?','original_purchase_date'=>'?','original_purchase_date_ms'=>'?','original_purchase_date_pst'=>'?','is_trial_period'=>'?'];
        $options4['param']=[$post['receipt_type'],$post['bid'],$post['bvrs'],$post['unique_vendor_identifier'],$post['item_id'],$post['quantity'],$post['product_id'],$post['transaction_id'],$post['original_transaction_id'],$post['purchase_date'],$post['purchase_date_ms'],$post['purchase_date_pst'],$post['original_purchase_date'],$post['original_purchase_date_ms'],$post['original_purchase_date_pst'],$post['is_trial_period']];
        $options4['table']='log_user_pay_apple';
        $status4=$this->db->add($tmpData4,$options4);
        if($status2!=FALSE&&$status4!=FALSE){
            $this->db->commit();
        }else{
            $this->db->rollback();
        }
    }
    
    //添加apple支付信息沙盒
    public function addLogUserPayAppleSandbox($payType,$post){
        $options1['table']= 'log_user_pay_apple';
        $options1['where']= ['transaction_id'=>'?'];
        $options1['param']= [$post['trade_no']];
        $tradeInfo=$this->db->find($options1);
        if($tradeInfo){
           return; 
        }        
        $tmpData4=['receipt_type'=>'?','bid'=>'?','bvrs'=>'?','unique_vendor_identifier'=>'?','item_id'=>'?','quantity'=>'?','product_id'=>'?','transaction_id'=>'?','original_transaction_id'=>'?','purchase_date'=>'?','purchase_date_ms'=>'?','purchase_date_pst'=>'?','original_purchase_date'=>'?','original_purchase_date_ms'=>'?','original_purchase_date_pst'=>'?','is_trial_period'=>'?'];
        $options4['param']=[$post['receipt_type'],$post['bid'],$post['bvrs'],$post['unique_vendor_identifier'],$post['item_id'],$post['quantity'],$post['product_id'],$post['transaction_id'],$post['original_transaction_id'],$post['purchase_date'],$post['purchase_date_ms'],$post['purchase_date_pst'],$post['original_purchase_date'],$post['original_purchase_date_ms'],$post['original_purchase_date_pst'],$post['is_trial_period']];
        $options4['table']='log_user_pay_apple';
        $status4=$this->db->add($tmpData4,$options4);
    }
    
    
    //添加银行支付记录
    public function addLogUserPayYeebank($payType,$post){
        $options1['table']= 'log_user_pay';
        $options1['where']= array('trade_no'=>'?','pay_type'=>'?');
        $options1['param']= array($post['yborderid'],$payType);
        $tradeInfo=$this->db->find($options1);
        if($tradeInfo){
            return;
        }
        $this->db->startTrans();
        $tmpData2=array('uid'=>'?','trade_no'=>'?','pay_type'=>'?','out_trade_no'=>'?','trade_status'=>'?','buyer_id'=>'?','price'=>'?','total_fee'=>'?','trade_time'=>'?','game_id'=>'?','game_uid'=>'?','game_server'=>'?','ctime'=>'?','cdate'=>'?');
        $options2['param']=array($post['uid'],$post['yborderid'],$payType,$post['orderid'],'SUCCESS',$post['merchantaccount'],$post['amount'],$post['amount'],time(),$post['game_id'],$post['game_uid'],$post['game_server'],time(),date('Y-m-d',time()));
        $options2['table']='log_user_pay';
        $status2=$this->db->add($tmpData2,$options2);
        $tmpData4=array();
        $options4['param']=array();
        foreach ($post as $k=>$v){
            if(!in_array($k, ['orderid','game_id','game_uid','game_server','uid','extra_common_param'])){
                $tmpData4[$k]='?';
                $options4['param'][]=$v;
            }
        }
        $options4['where']=['orderid'=>'?'];
        $options4['param'][]=$post['orderid'];
        $options4['table']='log_user_pay_yeebank';
        $status4=$this->db->save($tmpData4,$options4);
        if($status2!=FALSE&&$status4!=FALSE){
            $this->db->commit();
        }else{
            $this->db->rollback();
        }
    }
    
    //获得银行支付记录(分表预存信息)
    public function getPreLogUserPayYeebank($post){
        $options['table']='log_user_pay_yeebank';
        $options['field']='uid,game_id,game_uid,game_server,extra_common_param';
        $options['where']=['orderid'=>'?'];
        $options['param']=[$post['orderid']];
        $orderInfo=$this->db->find($options);
        return $orderInfo;
    }
    
    //添加银行支付记录(分表预存信息)
    public function preLogUserPayYeebank($post){
        if(empty($post['orderid']) || empty($post['uid'])|| empty($post['game_id'])|| empty($post['game_uid'])){
            return;
        }
        $tmpData=['orderid'=>'?','uid'=>'?','game_id'=>'?','game_uid'=>'?','game_server'=>'?','extra_common_param'=>'?'];
        $options['param']=[$post['orderid'],$post['uid'],$post['game_id'],$post['game_uid'],$post['game_server'],$post['extra_common_param']];
        $options['table']='log_user_pay_yeebank';
        $this->db->add($tmpData,$options);
    }
    
    //添加quicksdk支付信息(no use)
    public function addLogUserQuicksdk($payType,$post){
        if(isset($post['is_test']) && $post['is_test']==0){
            $options1['table']= 'log_user_pay';
            $options1['where']= array('trade_no'=>'?','pay_type'=>'?');
            $options1['param']= array($post['order_no'],$payType);
            $this->db->startTrans();
            $tradeInfo=$this->db->find($options1);
            if(empty($tradeInfo)){
                //0.uid,1.game_id,2.game_uid
                $tmp=explode('_', $post['extras_params']);
                $tmpData2=array('uid'=>'?','trade_no'=>'?','pay_type'=>'?','out_trade_no'=>'?','trade_status'=>'?','buyer_id'=>'?','price'=>'?','total_fee'=>'?','trade_time'=>'?','game_id'=>'?','game_uid'=>'?','game_server'=>'?','ctime'=>'?','cdate'=>'?');
                $options2['param']=array($tmp[0],$post['order_no'],$payType,$post['out_order_no'],'SUCCESS',0,$post['amount'],$post['amount'],strtotime($post['pay_time']),$tmp[1],$tmp[2],'',time(),date('Y-m-d',time()));
                $options2['table']='log_user_pay';
                $status=$this->db->add($tmpData2,$options2);
            }
            $this->db->commit();
        }
        $options3['table']='log_user_pay_quicksdk';
        $options3['where']=array('order_no'=>'?','out_order_no'=>'?');
        $options3['param']=array($post['order_no'],$post['out_order_no']);
        $result=$this->db->find($options3);
        if(!empty($result)){
            return;
        }
        $tmpData4=array();
        $options4['param']=array();
        foreach ($post as $k=>$v){
            $tmpData4[$k]='?';
            $options4['param'][]=$v;
        }
        $options4['table']='log_user_pay_quicksdk';
        $status=$this->db->add($tmpData4,$options4);
    }
        
    //添加用户登录日志(获取用户信息)
    public function addUserLogin($uid,$uname,$game_id,$game_uid,$oauthData){
        $param['uid']=$uid;
        $param['uname']=$uname;
        $param['game_id']=$game_id;
        $param['game_uid']=$game_uid;
        $param['channel_id']=$oauthData['channel_id'];
        $param['server_id']=isset($oauthData['server_id'])?$oauthData['server_id']:'';
        $param['ip']=$oauthData['ip'];
        $param['device_id']=$oauthData['device_id'];
        $param['oauth_token']=$oauthData['oauth_token'];
        $param['login_time']=time();
        $param['login_date']=date('Y-m-d',time());
        $param['localuid']=isset($oauthData['uid'])?$oauthData['uid']:'';
        $param['localuname']=isset($oauthData['uname'])?$oauthData['uname']:'';
        $param['localsessionid']=session_id();
        $param['localsessioninfo']=isset($_SESSION)?json_encode($_SESSION):'';
        
        $tmpData=['uid'=>'?','uname'=>'?','game_id'=>'?','game_uid'=>'?','channel_id'=>'?','server_id'=>'?','ip'=>'?','device_id'=>'?','oauth_token'=>'?','login_time'=>'?','login_date'=>'?','localuid'=>'?','localuname'=>'?','localsessionid'=>'?','localsessioninfo'=>'?'];
        $options['param']= array_values($param);
        $options['table']='log_user_login';
        $this->db->add($tmpData,$options);
        
        //unset($param['localsessioninfo']);
        //$postData['param']= json_encode($param);
        //$url='113.107.101.121:8015?action=tianyulog&gameflag=dzz';
        //$model1=new \Addons\Grab\GrabUtil();
        //$model1->single_grab_json_postdata($url,$postData);
    }
    
    //添加用户登录成功日志
    public function addUserLoginSuccess($oauthData,$userOauth){
        $param['input_uname']=isset($oauthData['account'])?$oauthData['account']:'';
        $param['input_oauth_token']=isset($oauthData['oauth_token'])?$oauthData['oauth_token']:'';
        $param['uid']=isset($userOauth['uid'])?$userOauth['uid']:'';
        $param['uname']=isset($userOauth['uname'])?$userOauth['uname']:'';
        $param['cellphone']=isset($userOauth['cellphone'])?$userOauth['cellphone']:'';
        $param['sessionid']=session_id();
        $param['sessioninfo']=isset($_SESSION)?json_encode($_SESSION):'';
        $param['ip']=isset($oauthData['ip'])?$oauthData['ip']:'';
        $param['device_id']=isset($oauthData['device_id'])?$oauthData['device_id']:'';
        $param['cdate']=date('Y-m-d H:i:s',time());
        $tmpData=['input_uname'=>'?','input_oauth_token'=>'?','uid'=>'?','uname'=>'?','cellphone'=>'?','sessionid'=>'?','sessioninfo'=>'?','ip'=>'?','device_id'=>'?','cdate'=>'?'];
        $options['table']='log_user_login_success';
        $options['param']= array_values($param);
        $this->db->add($tmpData,$options);
    }
    
    
    //添加cp通知日志
    public function addLogNotify($url,$tradeNo,$notifyData,$result=''){
        $notify=json_encode($notifyData);
        $tmpData=array('notify_url'=>'?','notify_trade_no'=>'?','notify_time'=>'?','notify_data'=>'?','notify_status'=>'?');
        $options['table']='log_notify';
        $options['param']=array($url,$tradeNo,time(),$notify,$result);
        $status=$this->db->add($tmpData,$options);
    }
    
    //添加用户设备1手机登录密码错误,2手机登录账号不存在,3用户名登录密码错误,4用户名登录账号不存在
    public function addUserLoginFailed($oauthData,$type){
        $tmpData=['uname'=>'?','cellphone'=>'?','password'=>'?','password1'=>'?','ctime'=>'?','type'=>'?'];
        $options['table']='log_user_login_failed';
        if(in_array($type,[1,2])){
            $options['param']=['',$oauthData['account'],$oauthData['password'],md5($oauthData['password']),time(),$type];
        }else{
            $options['param']=[$oauthData['account'],'',$oauthData['password'],md5($oauthData['password']),time(),$type];
        }
        $status=$this->db->add($tmpData,$options);      
    }
    
    //添加用户设备
    public function addUserDevice($oauthData){
        if(empty($oauthData['device_id'])){
            return;
        }
        if($oauthData['user_agent']=='ios'){
            $deviceType=1;
        }elseif($oauthData['user_agent']=='android'){
            $deviceType=2;
        }else{
            $deviceType=3;
        }
        $options1['table']='user_device';
        $options1['where']=['device_id'=>'?'];
        $options1['param']=[$oauthData['device_id']];
        $status1=$this->db->find($options1);
        if(!empty($status1)){
            return;
        }
        if($oauthData['imei']!=='' && $oauthData['imei']!=='null'){
            $iiMd5=md5($oauthData['imei']);
        }else{
            $iiMd5=md5($oauthData['idfa']);
        }
        $tmpData=['device_id'=>'?','device_type'=>'?','imei'=>'?','mac'=>'?','idfa'=>'?','user_agent'=>'?','ii_md5'=>'?','os'=>'?','os_version'=>'?','device'=>'?','devicetype'=>'?','screen'=>'?','mno'=>'?','nm'=>'?','app_version'=>'?','sdk_version'=>'?','addtime'=>'?','adddate'=>'?'];
        $options['param']=[$oauthData['device_id'],$deviceType,$oauthData['imei'],$oauthData['mac'],$oauthData['idfa'],$oauthData['http_user_agent'],$iiMd5,$oauthData['os'],$oauthData['os_version'],$oauthData['device'],$oauthData['devicetype'],$oauthData['screen'],$oauthData['mno'],$oauthData['nm'],$oauthData['app_version'],$oauthData['sdk_version'],time(),date('Y-m-d',time())];
        $options['table']='user_device';
        $status=$this->db->add($tmpData,$options);
    }
    
    //系统错误日志
    public function sysError($error){
        if(isset($_POST)&&!empty($_POST)){
            $postData=json_encode($_POST);
        }else{
            $postData=isset($_GET)?  json_encode($_GET):'';
        }
        $param['uid']=isset($_SESSION[SESSION_LOGGED_USERID])?$_SESSION[SESSION_LOGGED_USERID]:'';
        $param['ip']=$this->getRealIp();
        $param['user_agent']=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        $param['request_host']=isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'');
        $param['request_uri']=isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
        $param['sessionid']=session_id();
        $param['code']=isset($error['code'])?$error['code']:'';
        $param['message']=isset($error['message'])?serialize($error['message']):'';
        $param['data']=isset($error['data'])?serialize($error['data']):'';
        $param['postdata']=$postData;
        $param['cdate']=date('Y-m-d H:i:s',time());
        $tmpData=['uid'=>'?','ip'=>'?','user_agent'=>'?','request_host'=>'?','request_uri'=>'?','sessionid'=>'?','code'=>'?','message'=>'?','data'=>'?','postdata'=>'?','cdate'=>'?'];
        $options['param']=array_values($param);
        $options['table']='sys_error_log';
        $requestUri=['/client/Dzz_CY_11.apk','/favicon.ico'];
        if(in_array($param['request_uri'],$requestUri)){
            return ;
        }
        //$requestHost=['www.wanyouxi.com','dzz.wanyouxi.com'];
        $requestHost=['api.wanyouxi.com','api.91sd.com'];
        if(!in_array($param['request_host'],$requestHost)){
            return;
        }
        $status=$this->db->add($tmpData,$options);
        if($status===FALSE){
            exit(0);
        }
    }
     
    //获取ip
    protected function getRealIp(){
        $realip='';
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
                $realip=$_SERVER['HTTP_CLIENT_IP'];
            }else{
                $realip=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR')){
                $realip=getenv('HTTP_X_FORWARDED_FOR');
            }else if(getenv('HTTP_CLIENT_IP')){
                $realip=getenv('HTTP_CLIENT_IP');
            }else{
                $realip=getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }


    public function timeToDelete() {
        $sql = "
        DROP PROCEDURE IF EXISTS time_delete;
        CREATE PROCEDURE time_delete()
        BEGIN
            ##只保留15天的有效数据
            delete from oauth2_refresh_token where unix_timestamp(now()) - expires > 2592000;
            delete from oauth2_tokens where unix_timestamp(now()) - expires > 2592000;
            delete from log_game_download_detail where unix_timestamp(now()) - ctime > 2592000;
            delete from log_game_download_pv where unix_timestamp(now()) - ctime > 2592000;
            delete from log_user_login_success where unix_timestamp(now()) - unix_timestamp(cdate) > 2592000;
            delete from log_user_login where unix_timestamp(now()) - login_time > 2592000;
            delete from log_ios_distinct_idfa where unix_timestamp(now()) - ctime > 2592000;
        END";
         if ($this->db->create($sql)!== FALSE){
             //调用存储过程
             $sql ="CALL time_delete()";
             return $this->db->execute($sql);
         }
         return FALSE;
    }
}

