<?php
namespace Users;
class UserPayModel extends \Core\BaseModels {
    
    //通知游戏方
    //更新支付通知状态
    private function notifyGameServer($payType,$post){
        if($payType==1){
            $this->notifyGameServerAlipay($payType, $post);
        }elseif($payType==2){
            $this->notifyGameServerWechatpay($payType, $post);
        }elseif ($payType==3) {
            $this->notifyGameServerYeepay($payType, $post);
        }elseif ($payType==4) {
            $this->notifyGameServerApplepay($payType, $post);
        }elseif ($payType==5) {
            $this->notifyGameServerYeepaybank($payType, $post);
        }elseif ($payType==6) {
            $this->notifyGameServerQuicksdk($payType, $post);
        }
    }
    
    private function notifyGameServerAlipay($payType,$post){
        $model3=new \Log\LogModel();
        if(isset($post['trade_status']) && $post['trade_status']==='TRADE_SUCCESS'){
            if(!isset($post['extra_common_param']) || empty($post['extra_common_param'])){
                return;
            }                
            //$tmp=explode('_',$post['out_trade_no']); 
            $tmp1=explode('@#', $post['extra_common_param']);
            $tmp=explode('_', $tmp1[0]);
            $serverid=isset($tmp[3])?$tmp[3]:'';
            $model2=new \Users\UserInfoModel();
            $game=$model2->getGameInfo($tmp[1]);
            if(!isset($game['client_secret']) || empty($game['client_secret'])){
                return;
            }
            //统一发送通知参数           
            $notify=array('uid'=>$tmp[0],'trade_no'=>$post['trade_no'],'pay_type'=>'ALIPAY','out_trade_no'=>$post['out_trade_no'],'trade_status'=>$post['trade_status'],'buyer_id'=>$post['buyer_id'],'price'=>$post['price'],'total_fee'=>$post['total_fee'],'trade_time'=>strtotime($post['gmt_create']),'game_id'=>$tmp[1],'game_uid'=>$tmp[2],'game_server'=>$serverid,'ctime'=>time(),'extra_param'=>$tmp1[1]);
            $notify['client_secret']=$game['client_secret'];
            $model=new \Addons\Pay\PayUtil();
            $sysSign=$model->generateSign($notify);
            unset($notify['client_secret']);
            $notify['sys_sign']=$sysSign;
            //记录已发送消息
            $model3->addLogNotify($game['pay_redirect_uri'],$notify['trade_no'],$notify,'send');
            $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($game['pay_redirect_uri'],$notify);
            //直到收到success为止
            if(strtoupper($result)=='SUCCESS'){
                $this->updateNotify($notify['trade_no'], $payType);
                return 'SUCCESS';
            }
            return;               
        }
    }
    
    private function notifyGameServerWechatpay($payType,$post){
        $model3=new \Log\LogModel();
        if(isset($post['result_code']) && $post['result_code']=='SUCCESS' && isset($post['return_code']) && $post['return_code']=='SUCCESS'){
            if(!isset($post['attach']) || empty($post['attach'])){
                return;
            }
            $tmp1=explode('@#', $post['attach']);
            $tmp=explode('_',$tmp1[0]);
            $serverid=isset($tmp[3])?$tmp[3]:'';
            $model2=new \Users\UserInfoModel();
            $game=$model2->getGameInfo($tmp[1]);
            if(!isset($game['client_secret']) || empty($game['client_secret'])){
                return;
            }               
            //统一发送通知参数           
            $notify=array('uid'=>$tmp[0],'trade_no'=>$post['transaction_id'],'pay_type'=>'WECHATPAY','out_trade_no'=>$post['out_trade_no'],'trade_status'=>'SUCCESS','buyer_id'=>0,'price'=>$post['cash_fee']/100,'total_fee'=>$post['total_fee']/100,'trade_time'=>$post['time_end'],'game_id'=>$tmp[1],'game_uid'=>$tmp[2],'game_server'=>$serverid,'ctime'=>time(),'extra_param'=>$tmp1[1]);
            $notify['client_secret']=$game['client_secret'];
            $model=new \Addons\Pay\PayUtil();
            $sysSign=$model->generateSign($notify);
            unset($notify['client_secret']);
            $notify['sys_sign']=$sysSign;            
            //记录已发送消息
            $model3->addLogNotify($game['pay_redirect_uri'],$notify['trade_no'],$notify,'send');
            $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($game['pay_redirect_uri'],$notify);
            //直到收到success为止
            if(strtoupper($result)=='SUCCESS'){
                $this->updateNotify($notify['trade_no'], $payType);
                return 'SUCCESS';
            }
            return;
        }
    }
    
    private function notifyGameServerYeepay($payType,$post){
        $model3=new \Log\LogModel();
        if(isset($post['r1_Code']) && $post['r1_Code']==1){
            //0.uid,1.game_id,2.game_uid
            if(!isset($post['p9_MP']) || empty($post['p9_MP'])){
                return;
            }
            //$tmp1=explode('@#', $post['p9_MP']);
            $tmp1=explode('_yeepay_', $post['p9_MP']);
            $tmp=explode('_',$tmp1[0]);
            $serverid=isset($tmp[3])?$tmp[3]:'';
            $model2=new \Users\UserInfoModel();
            $game=$model2->getGameInfo($tmp[1]);
            if(!isset($game['client_secret']) || empty($game['client_secret'])){
                return;
            }               
            //统一发送通知参数           
            $notify=array('uid'=>$tmp[0],'trade_no'=>$post['r2_TrxId'],'pay_type'=>'YEEPAY','out_trade_no'=>$post['p2_Order'],'trade_status'=>'SUCCESS','buyer_id'=>0,'price'=>$post['p3_Amt'],'total_fee'=>$post['p7_realAmount'],'trade_time'=>time(),'game_id'=>$tmp[1],'game_uid'=>$tmp[2],'game_server'=>$serverid,'ctime'=>time(),'extra_param'=>$tmp1[1]);
            $notify['client_secret']=$game['client_secret'];
            $model=new \Addons\Pay\PayUtil();
            $sysSign=$model->generateSign($notify);
            unset($notify['client_secret']);
            $notify['sys_sign']=$sysSign;
            //记录已发送消息
            $model3->addLogNotify($game['pay_redirect_uri'],$notify['trade_no'],$notify,'send');
            $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($game['pay_redirect_uri'],$notify);
            //直到收到success为止
            if(strtoupper($result)=='SUCCESS'){
                $this->updateNotify($notify['trade_no'], $payType);
                return 'SUCCESS';
            }
            return;
        }
    }
    
    private function notifyGameServerApplepay($payType,$post){
        $model3=new \Log\LogModel();
        $model2=new \Users\UserInfoModel();
        $game=$model2->getGameInfo($post['game_id']);
        if(!isset($game['client_secret']) || empty($game['client_secret'])){
            return;
        }
        //统一发送通知参数           
        $notify=array('uid'=>$post['uid'],'trade_no'=>$post['trade_no'],'pay_type'=>'APPLEPAY','out_trade_no'=>$post['out_trade_no'],'trade_status'=>'SUCCESS','buyer_id'=>0,'price'=>$post['total_fee'],'total_fee'=>$post['total_fee'],'trade_time'=>time(),'game_id'=>$post['game_id'],'game_uid'=>$post['game_uid'],'game_server'=>$post['game_server'],'ctime'=>time(),'extra_param'=>$post['extra_common_param']);
        $notify['client_secret']=$game['client_secret'];
        $model=new \Addons\Pay\PayUtil();
        $sysSign=$model->generateSign($notify);
        unset($notify['client_secret']);
        $notify['sys_sign']=$sysSign;
        //记录已发送消息
        $model3->addLogNotify($game['pay_redirect_uri'],$notify['trade_no'],$notify,'send');
        $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($game['pay_redirect_uri'],$notify);
        //直到收到success为止
        if(strtoupper($result)=='SUCCESS'){
            $this->updateNotify($notify['trade_no'], $payType);
            return 'SUCCESS';
        }
        return;
    }
    
    private function notifyGameServerYeepaybank($payType, $post){
        $model3=new \Log\LogModel();
        if(isset($post['yborderid']) && !empty($post['yborderid'])){
            $model2=new \Users\UserInfoModel();
            $game=$model2->getGameInfo($post['game_id']);
            if(!isset($game['client_secret']) || empty($game['client_secret'])){
                return;
            }               
            //统一发送通知参数           
            $notify=array('uid'=>$post['uid'],'trade_no'=>$post['yborderid'],'pay_type'=>'YEEPAYBANK','out_trade_no'=>$post['orderid'],'trade_status'=>'SUCCESS','buyer_id'=>$post['merchantaccount'],'price'=>$post['amount'],'total_fee'=>$post['amount'],'trade_time'=>time(),'game_id'=>$post['game_id'],'game_uid'=>$post['game_uid'],'game_server'=>$post['game_server'],'ctime'=>time(),'extra_param'=>$post['extra_common_param']);
            $notify['client_secret']=$game['client_secret'];
            $model=new \Addons\Pay\PayUtil();
            $sysSign=$model->generateSign($notify);
            unset($notify['client_secret']);
            $notify['sys_sign']=$sysSign;
            //记录已发送消息
            $model3->addLogNotify($game['pay_redirect_uri'],$notify['trade_no'],$notify,'send');
            $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($game['pay_redirect_uri'],$notify);
            //直到收到success为止
            if(strtoupper($result)=='SUCCESS'){
                $this->updateNotify($notify['trade_no'], $payType);
                return 'SUCCESS';
            }
            return;
        }
    }
    
    //TODO
    private function notifyGameServerQuicksdk($payType,$post){
        $model3=new \Log\LogModel();
        if(isset($post['is_test']) && $post['is_test']==0){
            //0.uid,1.game_id,2.game_uid
            if(!isset($post['extras_params']) || empty($post['extras_params'])){
                return;
            }
            $tmp=explode('_',$post['extras_params']);
            $model2=new \Users\UserInfoModel();
            $game=$model2->getGameInfo($tmp[1]);
            if(!isset($game['client_secret']) || empty($game['client_secret'])){
                return;
            }               
            //统一发送通知参数           
            $notify=array('uid'=>$tmp[0],'trade_no'=>$post['order_no'],'pay_type'=>'QUICKSDK','out_trade_no'=>$post['out_order_no'],'trade_status'=>'SUCCESS','buyer_id'=>0,'price'=>$post['amount'],'total_fee'=>$post['amount'],'trade_time'=>time(),'game_id'=>$tmp[1],'game_uid'=>$tmp[2],'game_server'=>'','ctime'=>time());
            $notify['client_secret']=$game['client_secret'];
            $model=new \Addons\Pay\PayUtil();
            $sysSign=$model->generateSign($notify);
            unset($notify['client_secret']);
            $notify['sys_sign']=$sysSign;
            $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($game['pay_redirect_uri'],$notify);
            //记录已发送消息
            $model3->addLogNotify($game['pay_redirect_uri'],$notify['trade_no'],$notify,'send');
            //直到收到success为止
            if(strtoupper($result)=='SUCCESS'){
                $this->updateNotify($notify['trade_no'], $payType);
                return 'SUCCESS';
            }
            return;
        }
    }
    
    //统一异步支付通知
    public function unifyPayNotify(){
        
    }
    
    //更新支付通知状态
    private function updateNotify($tradeNo,$payType){
        $options['table']='log_user_pay';
        $options['field']='trade_no';
        $options['where']=['trade_no'=>'?','pay_type'=>'?','is_notify'=>'?'];
        $options['param']=[$tradeNo,$payType,0];
        //$options['lock']=true;
        $this->db->startTrans();
        $status=$this->db->find($options);
        $tmpData1=['is_notify'=>'?'];
        $options1['table']='log_user_pay';
        $options1['where']=['trade_no'=>'?','pay_type'=>'?'];
        $options1['param']=[1,$tradeNo,$payType];
        $status1=$this->db->save($tmpData1,$options1);
        if($status!==FALSE && $status1!==FALSE){
            $this->db->commit();
        }else{
            $this->db->rollback();
        }
    }
    
    //未收到成功的通知，再次通知游戏方(计划任务)
    public function reNotifyGameServer(){
        //查到未通知成功的订单，24个小时内通知6次,默认为成功。
        
    }
    

    //支付宝支付配置
    public function alipayConf(){
        $model=new \Addons\Pay\PayUtil();
        $data=$model->alipayConf();
        return $this->returnResult(200,$data);
    }
    
    //支付宝支付api
    public function alipayApi($post) {
        $model=new \Addons\Pay\PayUtil();
        return $model->alipayApi($post);
    }
    
    //支付宝wap支付api
    public function alipayWebApi($post){
        //$model=new \Addons\Pay\PayUtil();
        //return $model->alipayWebApi($post);
    }
    
    //支付宝及时到账(扫码支付)
    public function alipayDirectPayApi($post){
        $model=new \Addons\Pay\PayUtil();
        return $model->alipayDirectPayApi($post);
    }
    
     //支付宝扫码支付api
    public function alipayQRcodeApi($post){
        //$model=new \Addons\Pay\PayUtil();
        //return $model->alipayQRcodeApi($post);
    }
    
    //支付宝支付通知
    public function alipayNotifyUrl($post) {
        if(empty($post)){
            echo 'failed';exit;
        }
        $model=new \Addons\Pay\PayUtil();
        $verifyResult=$model->alipayNotifyUrl($post);//验证支付宝支付消息
        if($verifyResult){
            $post['extra_common_param']=urldecode($post['extra_common_param']);//兼容web
            $model1=new \Log\LogModel();
            $model1->addLogUserPay(1, $post);//添加支付日志                
            $result=$this->notifyGameServer(1,$post);//通知游戏支付
            if(strtoupper($result)=='SUCCESS'){
                echo 'success';
            }
            exit;
        } else {
            echo 'failed';
            exit;
        }
    }
        
    //微信支付配置
    public function wechatpayConf($clientId){
        $model=new \Addons\Pay\PayUtil();
        $data=$model->wechatpayConf($clientId);
        return $this->returnResult(200,$data);
    }
    
    //微信支付订单查询
    public function wechatpayTradeQuery($post){
        $model=new \Addons\Pay\PayUtil();
        $data=$model->wechatpayTradeQuery($post);
        return $this->returnResult(200,$data);
    }
   
    //微信支付api
    public function wechatpayApi($post) {
        $model=new \Addons\Pay\PayUtil();
        return $model->wechatpayApi($post);
    }
    
    //微信支付webapi（TODO移除）
    public function wechatpayWebApi($param) {      
        $post['out_trade_no'] = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        $post['detail']=$param['body'];
        $post['body']=$param['subject'];
        $post['total_fee']=$param['total_fee'];
        $post['attach']=$param['uid']."_".$param['game_id']."_".$param['game_uid']."@#".$param['extra_common_param'];
        $post['device_info']='WEB';
        $post['trade_type']='NATIVE';
        $post['spbill_create_ip']=$param['spbill_create_ip'];
        $model=new \Addons\Pay\PayUtil();
        $result=$model->wechatpayApi($post);
        if(isset($result['return_code']) && $result['return_code']=='SUCCESS' && $result['return_msg']=='OK'){
            return $result['code_url'];
        }else{
            return 'failed';
        }
    }
    
    //微信支付通知
    public function wechatpayNotifyUrl($post) {
        if(empty($post)){
            echo  json_encode(['return_code'=>'FAILED','return_msg'=>'']);
            exit;
        }
        $model=new \Addons\Pay\PayUtil();
        $verifyResult=$model->wechatpayNotifyUrl($post);//验证微信支付消息
        if($verifyResult){
            $model1=new \Log\LogModel();
            $model1->addLogUserPay(2,$post);//添加支付日志
            $result=$this->notifyGameServer(2,$post);//通知游戏支付
            if(strtoupper($result)=='SUCCESS'){
                echo  json_encode(['return_code'=>'SUCCESS','return_msg'=>'']);
            }
            exit;
        } else {
            echo  json_encode(['return_code'=>'FAILED','return_msg'=>'']);
            exit;
        }
    }
    
    //易宝支付配置
    public function yeepayConf(){
        $model=new \Addons\Pay\PayUtil();
        $data=$model->yeepayConf();
        return $this->returnResult(200,$data);
    }
    
    //易宝支付查询接口
    public function yeepayOrderQuery($post){
        $model=new \Addons\Pay\PayUtil();
        $data=$model->yeepayOrderQuery($post);
        return $this->returnResult(200,$data);
    }
    
    //易宝支付api
    public function yeepayApi($post) {
        if(!isset($post['p2_Order'])||!isset($post['p3_Amt'])||!isset($post['p4_verifyAmt'])||!isset($post['p5_Pid'])||!isset($post['p6_Pcat'])||!isset($post['p7_Pdesc'])||!isset($post['p8_Url'])||!isset($post['pa_MP'])||!isset($post['pa7_cardAmt'])||!isset($post['pa8_cardNo'])||!isset($post['pa9_cardPwd'])||!isset($post['pd_FrpId'])||!isset($post['pz_userId'])||!isset($post['pz1_userRegTime'])){
            return $this->returnResult(4300);
        }
        $model=new \Addons\Pay\PayUtil();
        $result=$model->yeepayApi($post);
        if(!empty($result)){
            return $this->returnResult(200,$result);
        }else{
            return $this->returnResult(4040);
        }
    }
    
    //易宝支付webapi
    public function yeepayWebApi($post) {
        if(!isset($post['p2_Order'])||!isset($post['p3_Amt'])||!isset($post['p4_verifyAmt'])||!isset($post['p5_Pid'])||!isset($post['p6_Pcat'])||!isset($post['p7_Pdesc'])||!isset($post['p8_Url'])||!isset($post['pa_MP'])||!isset($post['pa7_cardAmt'])||!isset($post['pa8_cardNo'])||!isset($post['pa9_cardPwd'])||!isset($post['pd_FrpId'])||!isset($post['pz_userId'])||!isset($post['pz1_userRegTime'])){
            return $this->returnResult(4300);
        }
        //p5_Pid,p6_Pcat,p7_Pdesc中文不能有充值，可以有金币
        //$post['p8_Url']='http://api1.wanyouxi.com/api/pay/yeepayNotifyUrl';         // 测试用，测试完注释
        $model=new \Addons\Pay\PayUtil();
        $result=$model->yeepayApi($post);
        //print_r($result);
        if(!empty($result)&&isset($result['r1_Code'])){
            if($result['r1_Code'] == "1"){
                //echo "<br>提交成功!";
                //echo "<br>商户订单号:" . $r6_Order . "<br>";
                sleep(5);
                //$post['p9_MP']=$post['pa_MP'];
                //$data=$this->yeepayTradeQuery($post);
                $data=$this->yeepayOrderQuery($post);
                //print_r($data);exit;
                $msg = '';
                if(strstr($data['data'],'r1_Code=1') || strstr($data['data'],'r1_Code=3')){
                    $msg = "支付成功!";
                }else {
                    $msg = "支付失败!";
                }
                echo "<script>alert('".$msg."');history.go(-1);</script>";
            }elseif($result['r1_Code'] == "2"){
                echo '<script>alert("提交失败!支付卡密无效!");history.go(-1);</script>';
            }elseif($result['r1_Code'] == "7"){
                echo '<script>alert("提交失败!支付卡密无效!");history.go(-1);</script>';
            }else{
                echo '<script>alert("提交失败!");history.go(-1);</script>';
            }
        }else{
            echo '<script>alert("提交失败!");history.go(-1);</script>';
        }
        exit;
    }
    
    //易宝支付银行卡
    public function yeepaybankApi($post) {
        $model1=new \Log\LogModel();
        $model1->preLogUserPayYeebank($post);
        $model=new \Addons\Pay\PayUtil();
        $data=$model->yeepayBankApi($post);
        return $this->returnResult(200,$data);
    }

    //易宝支付通知
    public function yeepayNotifyUrl($post) {
        $model=new \Addons\Pay\PayUtil();
        $verifyResult=$model->yeepayNotifyUrl($post);
        if($verifyResult){
            $model1=new \Log\LogModel();
            if($post['r1_Code']==1){
                //支付成功
                $model1->addLogUserPay(3, $post);//添加支付日志            
                $result=$this->notifyGameServer(3,$post);//通知游戏支付
                if(strtoupper($result)=='SUCCESS'){
                    echo "success";
                }
            }elseif($post['r1_Code']==2){
                //支付失败
                $model1->addLogUserPay(3, $post);//添加支付日志
                echo "success";
            }
            exit;
        }else{
            echo "failed";
            exit;
        }
    }
    
    //易宝银行卡支付通知
    public function yeepaybankNotifyUrl($post){
        $model=new \Addons\Pay\PayUtil();
        $verifyResult=$model->yeepayCallbackApi($post);
        file_put_contents(SITE_PATH."/data/logs.log",var_export($verifyResult,true)."\r\n",FILE_APPEND);   // 日志
        if(!empty($verifyResult)){
            $model1=new \Log\LogModel();
            $orderInfo=$model1->getPreLogUserPayYeebank($verifyResult);//找到不到订单信息
            if(empty($orderInfo)){
                return;
            }
            //uid,game_id,game_uid,extra_common_param
            $verifyResult['uid']=isset($orderInfo['uid'])?$orderInfo['uid']:'';
            $verifyResult['game_id']=isset($orderInfo['game_id'])?$orderInfo['game_id']:'';
            $verifyResult['game_uid']=isset($orderInfo['game_uid'])?$orderInfo['game_uid']:'';
            $verifyResult['game_server']=isset($orderInfo['game_server'])?$orderInfo['game_server']:'';
            $verifyResult['extra_common_param']=isset($orderInfo['extra_common_param'])?$orderInfo['extra_common_param']:'';     
            $verifyResult['amount']=$verifyResult['amount']/100;
            
            $model1->addLogUserPay(5, $verifyResult);//添加支付日志            
            $result=$this->notifyGameServer(5,$verifyResult);//通知游戏支付
            if(strtoupper($result)=='SUCCESS'){
                echo "success";
                exit;
            }
        }
        echo "failed";
        exit;
    }
    
    //易宝支付查询订单结果
    public function yeepayTradeQuery($post){
        if(empty($post['p2_Order'])||empty($post['pd_FrpId'])||empty($post['pa8_cardNo'])||empty($post['p9_MP'])){
            return $this->returnResult(4300);
        }
        $options1['table']='log_user_pay_yee';
        $options1['field']='trx_id,order,card_no,code,card_status';
        $options1['where']=array('order'=>'?','frp_id'=>'?','card_no'=>'?','mp'=>'?');
        $options1['param']=array($post['p2_Order'],$post['pd_FrpId'],$post['pa8_cardNo'],$post['p9_MP']);
        $result=$this->db->find($options1);
        if(!empty($result)){
            if($result['code']==1 && $result['card_status']==0){
                return $this->returnResult(200,$result);
            }else{
                return $this->returnResult(202,$result);
            }
        }else{
            return $this->returnResult(400);
        }
    }
    
    //quicksdk异步支付通知
    public function quicksdkPayNotify($post){
        $model=new \Addons\Quicksdk\QuicksdkUtil();
        $data=$model->quicksdkPayNotify($post);
        if(isset($data['code'])){
            return $this->returnResult(4000);
        }
        $model1=new \Log\LogModel();
        $model1->addLogUserPay(4,$data);//添加支付日志            
        $result=$this->notifyGameServer(4,$data);//通知游戏支付
    }
    
    /**
        * 21000 App Store不能读取你提供的JSON对象
        * 21002 receipt-data域的数据有问题
        * 21003 receipt无法通过验证
        * 21004 提供的shared secret不匹配你账号中的shared secret
        * 21005 receipt服务器当前不可用
        * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
        * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
        * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
        */
    //苹果异步支付通知
    public function applepayNotifyUrl($post){
        if(empty($post['uid'])||empty($post['game_id'])||empty($post['game_uid']) || empty($post['out_trade_no'])){
            return $this->returnResult(4300);
        }
        $model=new \Addons\Pay\PayUtil();
        $verifyResult=$model->applepayNotifyUrl($post);
        if(isset($verifyResult['status']) && $verifyResult['status']==0){        
            $receipt=$verifyResult['receipt'];           
            $post['receipt_type']=$verifyResult['environment'];
            $post['bid']=$receipt['bundle_id'];
            $post['bvrs']=$receipt['application_version'];
            $post['unique_vendor_identifier']='';
            $post['item_id']='';
            $tmp=explode('RMB', $receipt['in_app'][0]['product_id']);
            $post['trade_no']=$receipt['in_app'][0]['original_transaction_id'];
            if($post['amount']){        // 11.8添加
                $post['total_fee']= $post['amount'];
            }else{
                $post['total_fee']= isset($tmp[1])?$tmp[1]:0;
            }
            //$post['total_fee']= isset($tmp[1])?$tmp[1]:0;
            $post=array_merge($post,$receipt['in_app'][0]);             
            $model1=new \Log\LogModel();          
            if(isset($verifyResult['environment']) && $verifyResult['environment']=='Sandbox'){
                $model1->addLogUserPayAppleSandbox(4, $post);//添加支付日志
            }else{
                $model1->addLogUserPay(4, $post);//添加支付日志
            }        
            $result=$this->notifyGameServer(4,$post);//通知游戏支付
                
            /*if(isset($verifyResult['environment']) && $verifyResult['environment']=='Sandbox'){
              }else{//不是sandbox模式
                $post=array_merge($post,$receipt);
                $tmp=explode('RMB', $receipt['product_id']);
                $post['trade_no']=$receipt['original_transaction_id'];
                $post['total_fee']= isset($tmp[1])?$tmp[1]:0;
                $post['receipt_type']='Production';
                $post['is_trial_period']='';
                $model1=new \Log\LogModel();          
                $model1->addLogUserPay(4, $post);//添加支付日志            
                $result=$this->notifyGameServer(4,$post);//通知游戏支付
            }*/
            if(strtoupper($result)=='SUCCESS'){
                return $this->returnResult(200);
            }else{
                return $this->returnResult(4000);
            }
        }else{
            return $this->returnResult(4041,$verifyResult['status']);
        }
    }
    
}

