<?php
class PayController extends \Core\BaseControllers {
     
    public function init() {
        parent::init();       
    }
    
    //验证身份
    private function verifyIdentity() {
        if(ALLOW_OAUTH2){
            $model=new \Addons\Oauth2\PDOOAuth2();
            $token=$model->verifyAccessTokenJson();//oauth_token正确
            //$clientSecret=$model->getClientSecret($token['client_id']);
            $gameInfo=$model->getGameInfo($token['client_id']);
            parent::verifySign($gameInfo['client_secret']);//签名正确
            $this->_clientId=$token['client_id'];
            $this->_clientSecret=$gameInfo['client_secret'];
            unset($gameInfo['client_secret']);
            $this->_gameInfo=$gameInfo;
            if($this->_gameInfo['game_id']==11114){//TODOsession11114
                $_SESSION['dzz_game_id']=$this->_gameInfo['game_id'];
            }else{
                $_SESSION['dzz_game_id']='';
            }
        }
        if($this->_mid<=0){
            \Core\BaseErrors::ErrorHandler(4039);
        }
    }
    
    //平台统一订单号生成
    public function getOutTradeNoAction(){
        $this->verifyIdentity();
        $outTradeNo = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        $data=array('code'=>200,'message'=>'Success','data'=>array('out_trade_no'=>$outTradeNo));
        $this->returnValue($data);
    }
    
    //统一支付接口(web使用)
    public function unifyPayApiAction(){
        $origin=isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'';
        $allowOrigin=['http://mytest.wanyouxi.com','http://www.91sd.com','http://wap.91sd.com'];
        if(in_array($origin,$allowOrigin)){
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Credentials: true');
        }
        //$this->verifyIdentity();
        $post['serverid']=isset($this->_postData['serverid']) ? $this->_postData['serverid']: (isset($this->_getData['serverid']) ? $this->_getData['serverid']: '');
        $post['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: (isset($this->_getData['uid']) ? $this->_getData['uid']: $this->_mid);
        $post['pay_type']=isset($this->_postData['pay_type']) ? intval($this->_postData['pay_type']): (isset($this->_getData['pay_type']) ? intval($this->_getData['pay_type']): 1);
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: (isset($this->_getData['game_id']) ? $this->_getData['game_id']: '');
        $post['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: (isset($this->_getData['game_uid']) ? $this->_getData['game_uid']: '');
        $post['extra_common_param']=isset($this->_postData['extra_common_param']) ? $this->_postData['extra_common_param']: (isset($this->_getData['extra_common_param']) ? $this->_getData['extra_common_param']: '');
        $post['subject']=isset($this->_postData['subject']) ? $this->_postData['subject']: (isset($this->_getData['subject']) ? $this->_getData['subject']: '');
        $post['total_fee']=isset($this->_postData['total_fee']) ? $this->_postData['total_fee']: (isset($this->_getData['total_fee']) ? $this->_getData['total_fee']: '');
        $post['show_url']=isset($this->_postData['show_url']) ? $this->_postData['show_url']: (isset($this->_getData['show_url']) ? $this->_getData['show_url']: '');
        $post['body']=isset($this->_postData['body']) ? $this->_postData['body']: (isset($this->_getData['body']) ? $this->_getData['body']: '');
        $post['out_trade_no'] = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        if($post['game_id']==11114){//TODOsession11114
            $_SESSION['dzz_game_id']=11114;
        }else{
            $_SESSION['dzz_game_id']='';
        }
        //$_SESSION['dzz_game_id']='';
        if($post['pay_type']==1){//支付宝支付
            $model=new \Users\UserPayModel();
            $qrcode=isset($this->_postData['qrcode']) ? $this->_postData['qrcode']: (isset($this->_getData['qrcode']) ? $this->_getData['qrcode']: '1');
            $post['return_url']=isset($this->_postData['return_url']) ? $this->_postData['return_url']: (isset($this->_getData['return_url']) ? $this->_getData['return_url']: '');
            if($qrcode!=''){//支付宝扫码支付
                $post['qr_pay_mode']=$qrcode=='0'?'0':'1';
                $url=$model->alipayDirectPayApi($post);
                echo $url;
                exit;
            }
            $url=$model->alipayApi($post);
            header("Location:".$url);
        }elseif($post['pay_type']==2){//微信支付
            $post['detail']=$post['body'];
            $post['body']=$post['subject'];
            $post['total_fee']=$post['total_fee']*100;
            $post['attach']=$post['uid']."_".$post['game_id']."_".$post['game_uid']."_".$post['serverid']."@#".$post['extra_common_param'];
            $post['device_info']='WEB';
            $post['trade_type']='NATIVE';
            $post['spbill_create_ip']=$this->_realIp;
            unset($post['uid']);
            unset($post['game_id']);
            unset($post['game_uid']);
            unset($post['extra_common_param']);
            unset($post['subject']);
            unset($post['show_url']);
            unset($post['pay_type']);
            unset($post['serverid']);
            $model=new \Users\UserPayModel();
            $result=$model->wechatpayApi($post);
            if(isset($result['return_code']) && $result['return_code']=='SUCCESS' && $result['return_msg']=='OK'){
                //\Addons\Qrcode\QrcodeUtil::show($result['code_url']);
                echo $result['code_url'];
            }else{
                //\Addons\Qrcode\QrcodeUtil::show('failed');
                echo 'failed';
            }
        }elseif($post['pay_type']==3){
            $post['p2_Order']=$post['out_trade_no'];
            $post['p3_Amt']=$post['total_fee'];
            $post['p4_verifyAmt']=false;
            $post['p5_Pid']=$post['subject'];
            $post['p6_Pcat']='';
            $post['p7_Pdesc']=$post['body'];
            $post['p8_Url']='';
            $post['pa_MP']=$post['uid']."_".$post['game_id']."_".$post['game_uid']."_".$post['serverid']."_yeepay_".$post['extra_common_param'];
            $post['pa7_cardAmt']=isset($this->_postData['pa7_cardAmt']) ? $this->_postData['pa7_cardAmt']: '';
            $post['pa8_cardNo']=isset($this->_postData['pa8_cardNo']) ? $this->_postData['pa8_cardNo']: '';
            $post['pa9_cardPwd']=isset($this->_postData['pa9_cardPwd']) ? $this->_postData['pa9_cardPwd']: '';
            $post['pd_FrpId']=isset($this->_postData['pd_FrpId']) ? $this->_postData['pd_FrpId']: '';
            $post['pz_userId']='100001';
            $post['pz1_userRegTime']='2009-01-01 00:00:00';
            unset($post['serverid']);
            $model=new \Users\UserPayModel();
            $model->yeepayWebApi($post);
            //$this->returnValue($data);
        }elseif($post['pay_type']==5){
            $post['orderid']=isset($this->_postData['orderid']) ? $this->_postData['orderid']: $post['out_trade_no'];
            $post['transtime']=time();
            $post['amount']=$post['total_fee'];
            $post['productcatalog']=1;
            $post['productname']=$post['subject'];
            $post['productdesc']=$post['body'];
            $post['identitytype']=2;
            $post['identityid']=$post['uid'];
            $post['userip']=$this->_realIp;
            $post['terminaltype']=3;//0IMEI,1MAC,2UUID,3OTHER
            $post['terminalid']=$post['uid'];
            $post['fcallbackurl']='';
            $post['game_server']=$post['serverid'];
            $post['is_pc']=1;
            unset($post['subject']);
            unset($post['show_url']);
            unset($post['pay_type']);
            unset($post['serverid']);
            $model=new \Users\UserPayModel();
            $data=$model->yeepaybankApi($post);
            //$this->returnValue($data);
            header("Location:".$data['data']);
        }
    }
    
    ###################################################################################################
    ##########################################alipay###################################################
    ###################################################################################################
    //获得支付宝配置信息
    public function alipayConfAction(){
        $this->verifyIdentity();
        $model=new \Users\UserPayModel();
        $data=$model->alipayConf();
        $this->returnValue($data);
    }
    
    //支付宝支付//noused fortest
    public function alipayApiAction(){
        //$this->verifyIdentity();
        $post['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: $this->_mid;
        $post['pay_type']=isset($this->_postData['pay_type']) ? intval($this->_postData['pay_type']): 1;
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: '';
        $post['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $post['serverid']=isset($this->_postData['serverid']) ? $this->_postData['serverid']:'';
        $post['extra_common_param']=isset($this->_postData['extra_common_param']) ? $this->_postData['extra_common_param']: '';
        $post['subject']=isset($this->_postData['subject']) ? $this->_postData['subject']: '';
        $post['total_fee']=isset($this->_postData['total_fee']) ? $this->_postData['total_fee']: '';
        $post['show_url']=isset($this->_postData['show_url']) ? $this->_postData['show_url']: '';
        $post['body']=isset($this->_postData['body']) ? $this->_postData['body']: '';
        $post['out_trade_no'] = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        $model=new \Users\UserPayModel();
        $model->alipayWebApi($post);
    }
    
    //支付宝及时到账(扫码支付)//noused,fortest
    public function alipayDirectPayApiAction(){
        $post['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: $this->_mid;
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: '';
        $post['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $post['serverid']=isset($this->_postData['serverid']) ? $this->_postData['serverid']:'';
        $post['extra_common_param']=isset($this->_postData['extra_common_param']) ? $this->_postData['extra_common_param']: '';
        $post['subject']=isset($this->_postData['subject']) ? $this->_postData['subject']: '';
        $post['total_fee']=isset($this->_postData['total_fee']) ? $this->_postData['total_fee']: '';
        $post['body']=isset($this->_postData['body']) ? $this->_postData['body']: '';
        $post['return_url']=isset($this->_postData['return_url']) ? $this->_postData['return_url']: '';
        $post['out_trade_no'] = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        $_SESSION['dzz_game_id']=11114;
        $model=new \Users\UserPayModel();
        $data=$model->alipayDirectPayApi($post);
        echo $data;
    }
    
    //支付宝扫码支付//noused,fortest
    public function alipayQRcodeApiAction(){
        //$this->verifyIdentity();
        $_SESSION['dzz_game_id']=11114;
        $post['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: $this->_mid;
        $post['pay_type']=isset($this->_postData['pay_type']) ? intval($this->_postData['pay_type']): 1;
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: '';
        $post['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $post['serverid']=isset($this->_postData['serverid']) ? $this->_postData['serverid']:'';
        $post['extra_common_param']=isset($this->_postData['extra_common_param']) ? $this->_postData['extra_common_param']: '';
        $post['subject']=isset($this->_postData['subject']) ? $this->_postData['subject']: '';
        $post['total_fee']=isset($this->_postData['total_fee']) ? $this->_postData['total_fee']: '';
        $post['show_url']=isset($this->_postData['show_url']) ? $this->_postData['show_url']: '';
        $post['body']=isset($this->_postData['body']) ? $this->_postData['body']: '';
        $post['out_trade_no'] = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        $model=new \Users\UserPayModel();
        $model->alipayQRcodeApi($post);
    }
    
    //支付宝返回//noused
    public function alipayReturnUrlAction(){
        //$model=new \Addons\Pay\PayUtil();
        //$model->alipayReturnUrl();
        //echo "<script>window.parent.location.href='http://mytest.wanyouxi.com';</script>";
    }
    
    //支付宝异步通知
    public function alipayNotifyUrlAction(){
        /*$word='alipay:'.$this->createLinkstring($this->_postData);
        $this->logResult($word); */   
        $post=$this->_postData;
        $model=new \Users\UserPayModel();
        $model->alipayNotifyUrl($post);
    }
    
    //支付宝异步通知
    public function alipayNotifyUrlChenyouAction(){
        //file_put_contents(SITE_PATH."/data/alilogs.log",json_encode($this->_postData)."\r\n",FILE_APPEND);   // 日志
        $word='alipay:'.$this->createLinkstring($this->_postData);
        $this->logResult($word); 
        $post=$this->_postData;
        $_SESSION['dzz_game_id']=11114;//TODOsession11114
        $model=new \Users\UserPayModel();
        $model->alipayNotifyUrl($post);
    }
    
    //支付宝同步回调
    public function alipayReturnUrlChenyouAction(){
        echo "<script>alert('充值成功！');</script>";
        //header('Location:http://www.91sd.com/dzz/unifypay');      // 11.18 注释
        header('Location:http://www.91sd.com');
    }
    
    //手机测试页
    public function alipayIndexAction(){
        //extension_loaded('openssl') or die('php需要openssl扩展支持');
        $this->display('paytest');
    }
    
    ###################################################################################################
    #########################################wechatpay#################################################
    ###################################################################################################
    //微信支付//noused
    public function wechatApiAction(){
        $this->verifyIdentity();
        //$_SESSION['dzz_game_id']='';
        $post1['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: $this->_mid;
        $post1['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: '';
        $post1['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $post1['extra_common_param']=isset($this->_postData['extra_common_param']) ? $this->_postData['extra_common_param']: '';      
        $post['body']=isset($this->_postData['body']) ? $this->_postData['body']: '';
        $post['detail']=isset($this->_postData['detail']) ? $this->_postData['detail']: '';
        $post['total_fee']=isset($this->_postData['total_fee']) ? $this->_postData['total_fee']: '';
        $post['attach']=$post1['uid']."_".$post1['game_id']."_".$post1['game_uid']."@#".$post1['extra_common_param'];
        $post['device_info']='WEB';
        $post['trade_type']='NATIVE';//JSAPI
        //$post['trade_type']='APP';
        $post['spbill_create_ip']=$this->_realIp;
        $post['out_trade_no'] = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        $model=new \Users\UserPayModel();
        $result=$model->wechatpayApi($post);
        if(isset($result['return_code']) && $result['return_code']=='SUCCESS' && $result['return_msg']=='OK'){
            \Addons\Qrcode\QrcodeUtil::show($result['code_url']);
            //weixin：//wxpay/bizpayurl?sign=XXXXX&appid=XXXXX&mch_id=XXXXX&product_id=XXXXXX&time_stamp=XXXXXX&nonce_str=XXXXX
        }else{
            //\Addons\Qrcode\QrcodeUtil::show('failed');
            echo 'failed';
        }
    }
    
    //获得微信配置信息
    public function wechatpayConfAction(){
        $this->verifyIdentity();
        $model=new \Users\UserPayModel();
        $data=$model->wechatpayConf($this->_clientId);
        $this->returnValue($data);
    }
    
    //微信支付订单查询
    public function wechatpayTradeQueryAction(){        
        $this->verifyIdentity();
        $post=$this->_postData;
        $model=new \Users\UserPayModel();
        $data=$model->wechatpayTradeQuery($post);
        $this->returnValue($data);
    }
    
    //微信支付异步通知
    public function wechatpayNotifyUrlAction(){
        $fileContent=file_get_contents('php://input');
        $xmlResult = (array)simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        /*$result='';
        foreach ($xmlResult as $k=>$v){
            $result.=$k."=".$v."&";
        }
        $word1='wechatxml:'.$result;
        $this->logResult($word1);*/
        $model=new \Users\UserPayModel();
        $model->wechatpayNotifyUrl($xmlResult);
    }
    
    //微信支付异步通知
    public function wechatpayNotifyUrlChenyouAction(){
        $fileContent=file_get_contents('php://input');
        $xmlResult = (array)simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        /*$result='';
        foreach ($xmlResult as $k=>$v){
            $result.=$k."=".$v."&";
        }
        $word1='wechatxml:'.$result;
        $this->logResult($word1);*/
        $_SESSION['dzz_game_id']=11114;
        $model=new \Users\UserPayModel();
        $model->wechatpayNotifyUrl($xmlResult);
    }
     
    ###################################################################################################
    ##########################################yeepay###################################################
    ###################################################################################################
    //获得易宝支付配置信息
    public function yeepayConfAction(){
        $this->verifyIdentity();
        $model=new \Users\UserPayModel();
        $data=$model->yeepayConf();
        $this->returnValue($data);
    }
    
    //易宝支付api
    public function yeepayApiAction(){
        $this->verifyIdentity();
        $post=$this->_postData;
        if(!is_array($post['pa7_cardAmt'])){
            $post['pa7_cardAmt']=  explode(',', $post['pa7_cardAmt']);
        }
        if(!is_array($post['pa8_cardNo'])){
            $post['pa8_cardNo']=  explode(',', $post['pa8_cardNo']);
        }
        if(!is_array($post['pa9_cardPwd'])){
            $post['pa9_cardPwd']=  explode(',', $post['pa9_cardPwd']);
        }
        $model=new \Users\UserPayModel();
        $data=$model->yeepayApi($post);
        $this->returnValue($data);
    }
    
    //易宝支付异步通知
    public function yeepayNotifyUrlAction(){
        /*$word='yeetest:'.$this->createLinkstring($this->_getData);
        $this->logResult($word);*/
        $post=$this->_getData;
        $model=new \Users\UserPayModel();
        $model->yeepayNotifyUrl($post);
    }
    
    //易宝支付异步通知
    public function yeepayNotifyUrlChenyouAction(){
        //$word='yeetest:'.$this->createLinkstring($this->_getData);
        //$this->logResult($word);
        $post=$this->_getData;
        $_SESSION['dzz_game_id']=11114;//TODOsession11114
        $model=new \Users\UserPayModel();
        $model->yeepayNotifyUrl($post);
    }
    
    //易宝银行卡支付
    public function yeepaybankApiAction(){
        $this->verifyIdentity();
        $outTradeNo = date('YmdHis').round(microtime(true)*1000).substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        $post['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: $this->_mid;
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: '';
        $post['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $post['game_server']=isset($this->_postData['serverid']) ? $this->_postData['serverid']: '';
        $post['extra_common_param']=isset($this->_postData['extra_common_param']) ? $this->_postData['extra_common_param']: '';
        $post['orderid']=isset($this->_postData['orderid']) ? $this->_postData['orderid']: $outTradeNo;
        $post['transtime']=time();
        $post['amount']=isset($this->_postData['amount']) ? $this->_postData['amount']: '';
        $post['productcatalog']=isset($this->_postData['productcatalog']) ? $this->_postData['productcatalog']: 1;
        $post['productname']=isset($this->_postData['productname']) ? $this->_postData['productname']: '';
        $post['productdesc']=isset($this->_postData['productdesc']) ? $this->_postData['productdesc']: '';
        $post['identitytype']=isset($this->_postData['identitytype']) ? $this->_postData['identitytype']: 2;
        $post['identityid']=isset($this->_postData['identityid']) ? $this->_postData['identityid']: '';
        $post['userip']=$this->_realIp;
        $post['terminaltype']=isset($this->_postData['terminaltype']) ? $this->_postData['terminaltype']: 3;//0IMEI,1MAC,2UUID,3OTHER
        $post['terminalid']=isset($this->_postData['terminalid']) ? $this->_postData['terminalid']: '';
        $post['callbackurl']=isset($this->_postData['callbackurl']) ? $this->_postData['callbackurl']: '';
        $post['fcallbackurl']=isset($this->_postData['fcallbackurl']) ? $this->_postData['fcallbackurl']: '';         
        $model=new \Users\UserPayModel();
        $data=$model->yeepaybankApi($post);
        $this->returnValue($data);
    }
        
    //易宝银行卡支付异步通知(晨游专用)
    public function yeepaybankNotifyUrlChenyouAction(){
        /*$word='yeebanktest:'.$this->createLinkstring($this->_postData);
        $this->logResult($word);*/
        $post=$this->_postData;
        $_SESSION['dzz_game_id']=11114;//TODOsession11114
        $model=new \Users\UserPayModel();
        $model->yeepaybankNotifyUrl($post);
    }
    
    //易宝银行卡支付异步通知
    public function yeepaybankNotifyUrlAction(){
        $post=$this->_postData;
        $model=new \Users\UserPayModel();
        $model->yeepaybankNotifyUrl($post);
    }
    
    //易宝支付订单查询（易宝平台）
    public function yeepayOrderQueryAction(){
        $this->verifyIdentity();
        $post=$this->_postData;
        $model=new \Users\UserPayModel();
        $data=$model->yeepayOrderQuery($post);
        $this->returnValue($data);
    }
    
    //易宝支付查询订单结果（天豫平台）
    public function yeepayTradeQueryAction(){
        $this->verifyIdentity();
        $post=$this->_postData;
        $model=new \Users\UserPayModel();
        $data=$model->yeepayTradeQuery($post);
        if($data['code']==400){
            $data['code']=4000;
        }
        $this->returnValue($data);
    }
    
    //易宝支付web测试页
    public function yeepayIndexAction(){
        $this->display('yeepaytest');
    }
    
    ###################################################################################################
    ##########################################iospay###################################################
    ###################################################################################################
    //ios通知回调地址
    public function applepayNotifyUrlAction(){
        /*$word='applepaytest:'.$this->createLinkstring($this->_postData);
        $this->logResult($word);*/
        $this->verifyIdentity();
        $post['receipt_data']=isset($this->_postData['receipt_data']) ? $this->_postData['receipt_data']: '';
        $post['uid']=isset($this->_postData['uid']) ? $this->_postData['uid']: $this->_mid;
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: '';
        $post['game_uid']=isset($this->_postData['game_uid']) ? $this->_postData['game_uid']: '';
        $post['game_server']=isset($this->_postData['serverid']) ? $this->_postData['serverid']: '';
        $post['extra_common_param']=isset($this->_postData['extra_common_param']) ? $this->_postData['extra_common_param']: '';
        $post['out_trade_no'] = isset($this->_postData['out_trade_no']) ? $this->_postData['out_trade_no']: '';
        $post['amount'] = isset($this->_postData['amount']) ? $this->_postData['amount']: '';       // 11.8添加
        $model= new \Users\UserPayModel();
        $data=$model->applepayNotifyUrl($post);
        $this->returnValue($data);
        
    }
    
    //未收到成功的通知，再次通知游戏方(计划任务)
    public function reNotifyGameServerAction(){
        $model=new \Users\UserPayModel();
        $data=$model->reNotifyGameServer();
    }
        
    //模拟cp端接收通知成功
    public function payNotifyTestAction(){
        echo 'success';
    }
    
    //quicksdk异步支付通知
    public function quicksdkNotifyUrlAction(){
        //$word='quicksdktest:'.$this->createLinkstring($_REQUEST);
        //$this->logResult($word);
        $model=new \Users\UserPayModel();
        $model->quicksdkPayNotify($this->_postData);
    }
    
    //uc//查看服务器通知地址
        /*{
"sign":"ef8b9ed73dc13ab5e3f1eb6b306b34c5",
"data":{
"orderId":"20120321172811458",
"gameId":"5",
"serverId":"1",
"ucid":"56920",
"payWay":"1",
"amount":"50.0",
"callbackInfo":"goodid=xxx#user=xxx",
"orderStatus":"S",
"failedDesc":""}}
         */
    //统一异步支付通知//同一ip地址重复请求过滤
    public function unifyNotifyUrlAction(){
        $fileContent=file_get_contents("php://input");
        $fileContent=$_SERVER['REMOTE_ADDR'].$fileContent;
        $this->logResult($fileContent);
        $model=new \Users\UserPayModel();
        $model->unifyPayNotify($this->_postData);
    }
            
    
    
    //测试方法
    public function payredirectAction(){
        $word='newtest:'.$this->createLinkstring($this->_postData);
        $this->logResult($word);
    }
    
    //测试方法
    public function logResult($word=''){
        $fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
    }
    
    //测试方法
    public function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
		$arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);
	
	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	
	return $arg;
    }
    
}

