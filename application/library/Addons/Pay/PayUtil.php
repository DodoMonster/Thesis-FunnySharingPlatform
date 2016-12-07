<?php
namespace Addons\Pay;
class PayUtil {

    //HTTPS形式消息验证地址
    var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    //HTTP形式消息验证地址
    var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
    protected $_alipayConfig = [];
    protected $_wechatpayConfig = [];
    protected $_yeepayConfig = [];   
    protected $_applepayConfig=[];
    //rsa秘钥文件
    private $_privateKeyFilePath = 'alipay/pem/rsa_private_key.pem';
    private $_publicKeyFilePath = 'alipay/pem/rsa_public_key.pem';
    private $_privateKey = '';
    private $_publicKey = '';

    public function __construct() {
        //晨游收款端
        if(isset($_SESSION['dzz_game_id']) && $_SESSION['dzz_game_id']==11114){//TODOsession11114
            $this->_alipayConfig = \Yaf_Registry::get("chenyou_payconfig")->alipay->toArray();
            $this->_alipayConfig['cacert'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/cacert.pem';
            $this->_alipayConfig['ali_public_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem/alipay_public_key.pem';
            $this->_alipayConfig['private_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem/rsa_private_key.pem';
            $this->_wechatpayConfig = \Yaf_Registry::get("chenyou_payconfig")->wechatpay->toArray();
            $this->_yeepayConfig = \Yaf_Registry::get("chenyou_payconfig")->yeepay->toArray();
            $this->_applepayConfig = \Yaf_Registry::get("chenyou_payconfig")->applepay->toArray();
            $this->_alipayConfig['notify_url']='http://api.91sd.com/api/pay/alipayNotifyUrlChenyou';
            $this->_alipayConfig['return_url']='http://api.91sd.com/api/pay/alipayReturnUrlChenyou';
            $this->_wechatpayConfig['notify_url']='http://api.91sd.com/api/pay/wechatpayNotifyUrlChenyou';
            $this->_yeepayConfig['czk_notify_url']='http://api.91sd.com/api/pay/yeepayNotifyUrlChenyou';
            $this->_yeepayConfig['banknotify_url']='http://api.91sd.com/api/pay/yeepaybankNotifyUrlChenyou';
        }else{//天豫游戏收款端
            $this->_alipayConfig = \Yaf_Registry::get("payconfig")->alipay->toArray();
            $this->_alipayConfig['cacert'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/cacert.pem';
            $this->_alipayConfig['ali_public_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem/alipay_public_key.pem';
            $this->_alipayConfig['private_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem/rsa_private_key.pem';
            $this->_wechatpayConfig = \Yaf_Registry::get("payconfig")->wechatpay->toArray();
            $this->_yeepayConfig = \Yaf_Registry::get("payconfig")->yeepay->toArray();
            $this->_applepayConfig = \Yaf_Registry::get("payconfig")->applepay->toArray();
        }        
        
    }

    //rsa初始化//noused
    private function RSAinit() {
        extension_loaded('openssl') or die('php需要openssl扩展支持');
        (file_exists($this->_privateKeyFilePath) && file_exists($this->_publicKeyFilePath)) or die('密钥或者公钥的文件路径不正确');
        $this->_privateKey = openssl_pkey_get_private(file_get_contents($this->_privateKeyFilePath));
        $this->_publicKey = openssl_pkey_get_public(file_get_contents($this->_publicKeyFilePath));
        ($this->_privateKey && $this->_publicKey) or die('密钥或者公钥不可用');
    }

    //rsa加密//noused
    public function RSAEncrypt($privateKey, $originalData, $encryptData = '') {
        openssl_private_encrypt($originalData, $encryptData, $privateKey);
        return base64_encode($encryptData);
    }

    //rsa解密//noused
    public function RSADecrypt($publicKey, $encryptData, $decryptData = '') {
        openssl_public_decrypt($encryptData, $decryptData, $publicKey);
        return $decryptData;
    }

    //生成签名
    public function generateSign($params) {
        $secretKey = isset($params['client_secret']) ? $params['client_secret'] : '';
        if (empty($secretKey)) {
            BaseErrors::ErrorHandler(5007);
        }
        unset($params['client_secret']);
        ksort($params);
        $stringToBeSigned = $secretKey;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $secretKey;
        return strtoupper(md5($stringToBeSigned));
    }
    
    //生成微信签名
    public function generateWetchatpaySign($params, $key) {
        unset($params['sign']);
        ksort($params);
        $stringToBeSigned = '';
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= $k."=".$v."&";
            }
        }
        unset($k, $v);        
        $stringToBeSigned .= 'key=' . $key;
        return strtoupper(md5($stringToBeSigned));
    }
    
    //生成微信随机数
    private function generateRandStr($length){
        $str='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len=strlen($str)-1;
        $randstr='';
        for($i=0;$i<$length;$i++){
            $num=mt_rand(0,$len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
    
    ###################################################################################################
    ##########################################alipay###################################################
    ###################################################################################################
    //支付宝配置
    public function alipayConf() {
        $alipayConfig['partner'] = $this->_alipayConfig['partner'];
        $alipayConfig['seller_id'] = $this->_alipayConfig['seller_id'];
        $alipayConfig['key'] = $this->_alipayConfig['key'];
        $alipayConfig['notify_url'] = $this->_alipayConfig['notify_url'];
        return $alipayConfig;
    }

    //支付宝支付
    public function alipayApi($post) {
        $alipay_config = $this->_alipayConfig;
        unset($alipay_config['ali_public_key_path']);
        unset($alipay_config['private_key_path']);
        $alipay_config['sign_type']='MD5';//强制转换为MD5，APP为RSA
        require_once("alipay/lib/alipay_submit.class.php");
        //商户订单号，商户网站订单系统中唯一订单号，必填
        //$out_trade_no = $_POST['WIDout_trade_no'];
        $out_trade_no =$post['out_trade_no'];
        //订单名称，必填
        $subject = $post['subject'];
        //付款金额，必填
        $total_fee = $post['total_fee'];
        //收银台页面上，商品展示的超链接，必填
        $show_url = $post['show_url'];
        //商品描述，可空
        $body = $post['body'];

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $alipay_config['service'],
            "partner" => $alipay_config['partner'],
            "seller_id" => $alipay_config['seller_id'],
            "payment_type" => $alipay_config['payment_type'],
            "notify_url" => $alipay_config['notify_url'],
            "return_url" => $alipay_config['return_url'],
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "show_url" => $show_url,
            "body" => $body,
            "qr_pay_mode"=>'1',
            "extra_common_param"=>$post['uid']."_".$post['game_id']."_".$post['game_uid']."_".$post['serverid']."@#".$post['extra_common_param'],
                //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
                //如"参数名"	=> "参数值"   注：上一个参数末尾需要“,”逗号。
        );
        //$alipaySubmit = new \AlipaySubmit($alipay_config);
        //$html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        //echo $html_text;
        //直接请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $para=$alipaySubmit->buildRequestParaToString($parameter);
        $url='https://mapi.alipay.com/gateway.do?'.$para;
        return $url;
    }
    
    //支付宝支付//web端返回网站表单提交自动跳转(no used)
    public function alipayWebApi($post) {
        $alipay_config = $this->_alipayConfig;
        unset($alipay_config['ali_public_key_path']);
        unset($alipay_config['private_key_path']);
        $alipay_config['sign_type']='MD5';
        require_once("alipay/lib/alipay_submit.class.php");
        //商户订单号，商户网站订单系统中唯一订单号，必填
        //$out_trade_no = $_POST['WIDout_trade_no'];
        $out_trade_no =$post['out_trade_no'];
        //订单名称，必填
        $subject = $post['subject'];
        //付款金额，必填
        $total_fee = $post['total_fee'];
        //收银台页面上，商品展示的超链接，必填
        $show_url = $post['show_url'];
        //商品描述，可空
        $body = $post['body'];

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $alipay_config['service'],
            "partner" => $alipay_config['partner'],
            "seller_id" => $alipay_config['seller_id'],
            "payment_type" => $alipay_config['payment_type'],
            "notify_url" => $alipay_config['notify_url'],
            "return_url" => $alipay_config['return_url'],
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "show_url" => $show_url,
            "body" => $body,
            "extra_common_param"=>$post['uid']."_".$post['game_id']."_".$post['game_uid']."@#".$post['extra_common_param'],
            //"qr_pay_mode"=>1,
                //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
                //如"参数名"	=> "参数值"   注：上一个参数末尾需要“,”逗号。
        );
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        echo $html_text;
    }
    
    //支付宝及时到账(扫码支付)
    public function alipayDirectPayApi($post){ 
        $alipay_config = $this->_alipayConfig;
        unset($alipay_config['ali_public_key_path']);
        unset($alipay_config['private_key_path']);
        $alipay_config['sign_type']='MD5';
        require_once("alipay/lib/alipay_directpay.class.php");
        $parameter = array(
            "service" => 'create_direct_pay_by_user',
            "partner" => $alipay_config['partner'],
            "seller_id" => $alipay_config['seller_id'],
            "payment_type" => $alipay_config['payment_type'],
            "notify_url" => $alipay_config['notify_url'],
            //"notify_url" => "http://api1.wanyouxi.com/api/pay/alipayNotifyUrlChenyou",
            "return_url" => isset($post['return_url'])?$post['return_url']:$alipay_config['return_url'],
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "out_trade_no" => $post['out_trade_no'],
            "subject" => $post['subject'],
            "total_fee" => $post['total_fee'],
            "body" => $post['body'],
            "extra_common_param"=>$post['uid']."_".$post['game_id']."_".$post['game_uid']."_".$post['serverid']."@#".$post['extra_common_param'],
            "qr_pay_mode"=>$post['qr_pay_mode'],
            //"qr_pay_mode"=>'0',
        );
        $alipay = new \AlipayDirectpay($alipay_config);
        $para=$alipay->buildRequestParaToString($parameter);
        $url='https://mapi.alipay.com/gateway.do?'.$para;
        return $url;       
    }
    
    //支付宝扫码支付
    public function alipayQRcodeApi1($post){ 
        $alipay_config = $this->_alipayConfig;
        $alipay_config['private_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem_qrpay/rsa_private_key.pem';
        //$alipay_config['private_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem/rsa_private_key.pem';
        require_once("alipay/lib/alipay_precreate.class.php");
        
        $apiparameter=[
            "out_trade_no" => $post['out_trade_no'],
            "total_amount" => $post['total_fee'],
            "subject" => $post['subject'],
            "body" => $post['body'],
            //"extra_common_param"=>$post['uid']."_".$post['game_id']."_".$post['game_uid']."@#".$post['extra_common_param'],
        ];
        $biz_content=json_encode($apiparameter);
        $parameter=[
            "sign_type"=>'RSA',
            "app_id"=>'2016021401143769',
            "method"=>'alipay.trade.precreate',
            "charset"=>'utf-8',
            "timestamp"=>date("Y-m-d H:i:s",time()),
            "version"=>'1.0',
            "notify_url" => $alipay_config['notify_url'],
            "biz_content"=>$biz_content,
        ];    
              
        //直接请求
        $alipayprecreate = new \AlipayPrecreate($alipay_config);
        //$para=$alipayprecreate->buildRequestParaToString($apiparameter,$parameter);
        //$url='https://openapi.alipay.com/gateway.do?'.$para;
        $url=$alipayprecreate->buildRequestParaToString($apiparameter,$parameter);
        echo $url;
        $result=\Addons\Grab\GrabUtil::single_grab_getdata($url);
        //$url='https://openapi.alipay.com/gateway.do';
        //$result=\Addons\Grab\GrabUtil::single_grab_json_postdata($url,$para);
        print_r($result);
             
        exit;
        return $url;
    }
    
    //支付宝扫码支付
    public function alipayQRcodeApi($post){
        //require_once 'alipayqr/f2fpay/service/AlipayTradeService.php';
        require_once 'alipayqr/aop/AopClient.php';
        require_once 'alipayqr/aop/SignData.php';
        require_once 'alipayqr/aop/request/AlipayTradePrecreateRequest.php';
        $alipay_config = $this->_alipayConfig;
        $alipay_config['private_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem_qrpay/rsa_private_key.pem';
        $alipay_config['ali_public_key_path'] = APPLICATION_PATH . '/library/Addons/Pay/alipay/pem_qrpay/alipay_public_key.pem';
        $biz_content=[
            "out_trade_no" => $post['out_trade_no'],
            "total_amount" => $post['total_fee'],
            "subject" => $post['subject'],
            "body" => $post['body'],
            //"extra_common_param"=>$post['uid']."_".$post['game_id']."_".$post['game_uid']."@#".$post['extra_common_param'],
        ];
        //$biz_content=json_encode($biz_content,JSON_UNESCAPED_UNICODE);
        $biz_content=json_encode($biz_content);
        $parameter=[
            "app_id"=>'2016021401143769',
            "method"=>'alipay.trade.precreate',
            "charset"=>'utf-8',
            "timestamp"=>date("Y-m-d H:i:s",time()),
            "version"=>'1.0',
            "notify_url" => $alipay_config['notify_url'],
            "biz_content"=>$biz_content,
        ];
        /*$qrPay = new \AlipayTradeService($config);
	$qrPayResult = $qrPay->qrPay($parameter);
        print_r($qrPayResult);*/
        
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2016021401143769';
        $aop->rsaPrivateKeyFilePath = $alipay_config['private_key_path'];
        $aop->alipayPublicKey=$alipay_config['ali_public_key_path'];
        $aop->apiVersion = '1.0';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayTradePrecreateRequest();
        $request->setBizContent($biz_content);
        $result = $aop->execute ($request); 
        //$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        //$resultCode = $result->$responseNode->code;
        print_r($result);
    }

    //同步通知回调
    public function alipayReturnUrl() {
        $alipay_config = $this->_alipayConfig;
        require_once("alipay/lib/alipay_notify.class.php");
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if ($verify_result) {//验证成功
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //支付宝交易号
            $trade_no = $_GET['trade_no'];
            //交易状态
            $trade_status = $_GET['trade_status'];
            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }
            echo "验证成功<br />";
        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }

    //支付宝异步通知回调
    public function alipayNotifyUrl($post) {
        $alipayNotify = new \Addons\Pay\alipay\AlipayNotify($this->_alipayConfig);
        return $alipayNotify->verifyNotify($post);
    }

    //支付宝支付查询
    //https://doc.open.alipay.com/doc2/apiDetail.htm?spm=a219a.7629065.0.0.X017xO&apiId=757&docType=4
    public function alipayTradeQuery() {
        //https://openapi.alipay.com/gateway.do
    }

    ###################################################################################################
    #########################################wechatpay#################################################
    ###################################################################################################
    //微信配置//TODO按游戏端来分
    public function wechatpayConf($clientId = '') {
        $wechatpayConfig['appid'] = $this->_wechatpayConfig['appid'];
        $wechatpayConfig['appsecret'] = $this->_wechatpayConfig['appsecret'];
        $wechatpayConfig['mch_id'] = $this->_wechatpayConfig['mch_id'];
        $wechatpayConfig['key'] = $this->_wechatpayConfig['key'];
        $wechatpayConfig['notify_url'] = $this->_wechatpayConfig['notify_url'];
        return $wechatpayConfig;
    }

    //微信支付
    public function wechatpayApi($post) {
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        $appid=$this->_wechatpayConfig['appid'];
        $mch_id=$this->_wechatpayConfig['mch_id'];
        $notify_url=$this->_wechatpayConfig['notify_url'];
        $nonce_str=$this->generateRandStr(16);
        $params= array_merge(['appid'=>$appid,'mch_id'=>$mch_id,'nonce_str'=>$nonce_str,'notify_url'=>$notify_url],$post);
        $sign=$this->generateWetchatpaySign($params, $this->_wechatpayConfig['key']);
        $params['sign']=$sign;
        $xmlData=$this->arrayToXml($params);
        $result=\Addons\Grab\GrabUtil::single_grab_xml_postdata($url,$xmlData);
        return $result;
        /*
            [return_code] => SUCCESS
            [return_msg] => OK
            [appid] => wxde51df295a84fa2b
            [mch_id] => 1337789801
            [device_info] => WEB
            [nonce_str] => sXqPlDvQEyAt6jmz
            [sign] => F23D4F591D377F09477477A5070247E9
            [result_code] => SUCCESS
            [prepay_id] => wx20160804163445120c3e72570218575347
            [trade_type] => NATIVE
            [code_url] => weixin://wxpay/bizpayurl?pr=J5cwsyT
         */
    }

    
    //微信支付查询
    //https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_2&index=4
    public function wechatpayTradeQuery($post) {
        //$cert=APPLICATION_PATH . '/library/Addons/Pay/wechatpay/pem/apiclient_cert.pem';
        //$key=APPLICATION_PATH . '/library/Addons/Pay/wechatpay/pem/apiclient_key.pem';
        $url='https://api.mch.weixin.qq.com/pay/orderquery';
        $appid=$this->_wechatpayConfig['appid'];
        $mch_id=$this->_wechatpayConfig['mch_id'];
        $transaction_id=$post['transaction_id'];
        $nonce_str=$this->generateRandStr(16);
        $params=array('appid'=>$appid,'mch_id'=>$mch_id,'transaction_id'=>$transaction_id,'nonce_str'=>$nonce_str);
        $sign=$this->generateWetchatpaySign($params, $this->_wechatpayConfig['key']);
        $xmlData="<xml><appid>$appid</appid><mch_id>$mch_id</mch_id><nonce_str>$nonce_str</nonce_str><transaction_id>$transaction_id</transaction_id><sign>$sign</sign></xml>";
        $result=\Addons\Grab\GrabUtil::single_grab_xml_postdata($url,$xmlData);
        return $result;
    }

    //appid=wxde51df295a84fa2b&bank_type=CMB_DEBIT&cash_fee=1&fee_type=CNY&is_subscribe=N&mch_id=1337789801&nonce_str=3fOMzCbCZjJtIxau&openid=ohHvyvxtgwO4jcpRx82Xwj-xKMFk&out_trade_no=20160511183340495&result_code=SUCCESS&return_code=SUCCESS&sign=F2823F74E13063DC20D9652D7B497537&time_end=20160511183350&total_fee=1&trade_type=APP&transaction_id=4000562001201605115743057191&
    //微信支付验证通知:验证签名->查询交易状态->返回验证结果
    private function wechatpayVerifyNotify($post) {
        //验证签名
        $mySign=$this->generateWetchatpaySign($post, $this->_wechatpayConfig['key']);
        //$word='wechatsign:';
        //$word='my:'.$mySign.'###'.'other:'.$post['sign'];
        //$this->logResult($word);
        if($post['sign']==$mySign){
            //查询交易状态
            $query['transaction_id']=$post['transaction_id'];
            $result=$this->wechatpayTradeQuery($query);
            if($result['return_code']=='SUCCESS' && isset($result['trade_state']) && $result['trade_state']=='SUCCESS'){
                return TRUE;
            }
            return FALSE;
        }else{
            return FALSE;
        }
    }

    //微信支付异步通知回调
    public function wechatpayNotifyUrl($post) {
        return $this->wechatpayVerifyNotify($post);
    }

    ###################################################################################################
    #########################################yibaopay##################################################
    ###################################################################################################
    //易宝支付配置
    public function yeepayConf() {
        $yeepayConfig['czk_merchantId'] = $this->_yeepayConfig['czk_merchantId'];
        $yeepayConfig['czk_keyValue'] = $this->_yeepayConfig['czk_keyValue'];
        $yeepayConfig['czk_notify_url'] = $this->_yeepayConfig['czk_notify_url'];
        $yeepayConfig['merchantId'] = $this->_yeepayConfig['merchantId'];
        $yeepayConfig['keyValue'] = $this->_yeepayConfig['keyValue'];
        $yeepayConfig['notify_url'] = $this->_yeepayConfig['banknotify_url'];
        return $yeepayConfig;
    }
    
    //易宝支付订单查询(充值卡)
    public function yeepayOrderQuery($post){
        include_once 'yeepay/YeePayCommon.php';
        $p0_Cmd='ChargeCardQuery';
        $p1_MerId=$this->_yeepayConfig['czk_merchantId'];
        $p2_Order=$post['p2_Order'];
        $hmac=HmacMd5($p0_Cmd.$p1_MerId.$p2_Order,$this->_yeepayConfig['czk_keyValue']);
        $postfields=['p0_Cmd'=>$p0_Cmd,'p1_MerId'=>$p1_MerId,'p2_Order'=>$p2_Order,'hmac'=>$hmac];       
        $url='https://www.yeepay.com/app-merchant-proxy/command.action';
        $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($url,$postfields);
        return $result;
    }

    //易宝支付api(充值卡)
    public function yeepayApi($post) {
        include_once 'yeepay/YeePayCommon.php';
        $config=$this->_yeepayConfig;
        $p2_Order = $post['p2_Order'];
        #支付卡面额
        $p3_Amt = $post['p3_Amt'];
        #是否较验订单金额
        $p4_verifyAmt = $post['p4_verifyAmt'];
        #产品名称
        $p5_Pid = $post['p5_Pid'];
        #iconv("UTF-8","GBK//TRANSLIT",$_POST['p5_Pid']);
        #产品类型
        $p6_Pcat = $post['p6_Pcat'];
        #iconv("UTF-8","GBK//TRANSLIT",$_POST['p6_Pcat']);
        #产品描述
        $p7_Pdesc = $post['p7_Pdesc'];
        #iconv("UTF-8","GBK//TRANSLIT",$_POST['p7_Pdesc']);
        #商户接收交易结果通知的地址,易宝支付主动发送支付结果(服务器点对点通讯).通知会通过HTTP协议以GET方式到该地址上.	
        $p8_Url =$post['p8_Url']=!empty($post['p8_Url'])?$post['p8_Url']:$this->_yeepayConfig['czk_notify_url'];
        #临时信息
        $pa_MP = $post['pa_MP'];
        #iconv("UTF-8","GB2312//TRANSLIT",$_POST['pa_MP']);
        #卡面额
        $pa7_cardAmt = arrToStringDefault1($post['pa7_cardAmt']);
        #支付卡序列号.
        $pa8_cardNo = arrToStringDefault1($post['pa8_cardNo']);
        #支付卡密码.
        $pa9_cardPwd = arrToStringDefault1($post['pa9_cardPwd']);
        #支付通道编码
        $pd_FrpId = $post['pd_FrpId'];
        #应答机制
        $pr_NeedResponse = "1";
        #用户唯一标识
        $pz_userId = $post['pz_userId'];
        #用户的注册时间
        $pz1_userRegTime = $post['pz1_userRegTime'];


        #非银行卡支付专业版测试时调用的方法，在测试环境下调试通过后，请调用正式方法annulCard
        #两个方法所需参数一样，所以只需要将方法名改为annulCard即可
        #测试通过，正式上线时请调用该方法
        $result=annulCard($p2_Order, $p3_Amt, $p4_verifyAmt, $p5_Pid, $p6_Pcat, $p7_Pdesc, $p8_Url, $pa_MP, $pa7_cardAmt, $pa8_cardNo, $pa9_cardPwd, $pd_FrpId, $pz_userId, $pz1_userRegTime,$config);
        return $result;
    }

    //yeetest:api/pay/yeepayNotifyUrl=&r0_Cmd=ChargeCardDirect&r1_Code=2&p1_MerId=10013423030&p2_Order=20160518180118_1000000002_11112_123456&p3_Amt=0.0&p4_FrpId=SZX&p5_CardNo=asfasdf21s&p6_confirmAmount=0.0&p7_realAmount=0.0&p8_cardStatus=7&p9_MP=临时信息&pb_BalanceAmt=&pc_BalanceAct=&r2_TrxId=&hmac=ecce437a7efd83b2fb26f17bde2339a1
    //易宝支付异步回调地址(充值卡)
    public function yeepayNotifyUrl($post) {
        include_once 'yeepay/YeePayCommon.php';
        $config=$this->_yeepayConfig;
        $return = getCallBackValue($r0_Cmd, $r1_Code, $p1_MerId, $p2_Order, $p3_Amt, $p4_FrpId, $p5_CardNo, $p6_confirmAmount, $p7_realAmount, $p8_cardStatus, $p9_MP, $pb_BalanceAmt, $pc_BalanceAct, $hmac);
        #	判断返回签名是否正确（True/False）
        $bRet = CheckHmac($r0_Cmd, $r1_Code, $p1_MerId, $p2_Order, $p3_Amt, $p4_FrpId, $p5_CardNo, $p6_confirmAmount, $p7_realAmount, $p8_cardStatus, $p9_MP, $pb_BalanceAmt, $pc_BalanceAct, $hmac,$config);
        return $bRet;
        #	以上代码和变量不需要修改.
        #	校验码正确.
       /* if ($bRet) {
            
            echo "success";
            #在接收到支付结果通知后，判断是否进行过业务逻辑处理，不要重复进行业务逻辑处理
            if ($r1_Code == "1") {
                echo "<br>支付成功!";
                echo "<br>商户订单号:" . $p2_Order;
                echo "<br>支付金额:" . $p3_Amt;
                exit;
            } else if ($r1_Code == "2") {
                echo "<br>支付失败!";
                echo "<br>商户订单号:" . $p2_Order;
                exit;
            }
        } else {

            $sNewString = getCallbackHmacString($r0_Cmd, $r1_Code, $p1_MerId, $p2_Order, $p3_Amt, $p4_FrpId, $p5_CardNo, $p6_confirmAmount, $p7_realAmount, $p8_cardStatus, $p9_MP, $pb_BalanceAmt, $pc_BalanceAct);
            echo "<br>localhost:" . $sNewString;
            echo "<br>YeePay:" . $hmac;
            echo "<br>交易签名无效!";
            exit;
        }*/
    }
    
    //易宝支付银行卡api(银行卡)
    public function yeepayBankApi($post) {
        include("yeepay_bank/yeepay/yeepayMPay.php");
        //['ICBC','BOC','CCB','POST','ECITIC','CEB','HXB','CMBCHINA','CIB','SPDB','PINGAN','GDB','BCCB','SHB','CMBC','ABC','BOCO','GZCB'];
        //$productcatalogArray=[1=>'虚拟产品',3=>'公共事业缴费',4=>'手机充值',6=>'公益事业',7=>'实物电商',8=>'彩票业务',10=>'行政教育',11=>'线下服务业',13=>'微信实物电商',14=>'微信虚拟电商',15=>'保险行业',16=>'基金行业',17=>'电子票务',18=>'金融投资',19=>'大额支付',20=>'其他',21=>'旅游机票',22=>'畅付D'];
        //$identitytypeArray=[0=>'IMEI（International Mobile Equipment Identity）移动设备国际身份码的缩写',1=>'MAC 地址',2=>'用户ID',3=>'用户Email',4=>'用户手机号',5=>'用户身份证号',6=>'用户纸质订单协议号'];
        //include("yeepay_bank/config.php");
        $merchantaccount=$this->_yeepayConfig['merchantId'];
        $merchantPublicKey=$this->_yeepayConfig['merchant_publickey'];
        $merchantPrivateKey=$this->_yeepayConfig['merchant_privatekey'];
        $yeepayPublicKey=$this->_yeepayConfig['yeepay_publickey'];
           
        $yeepay = new \yeepayMPay($merchantaccount, $merchantPublicKey, $merchantPrivateKey, $yeepayPublicKey);
        $cardno = isset($post['cardno'])?trim($post['cardno']):'';
        $idcardtype = isset($post['idcardtype'])?trim($post['idcardtype']):'';
        $idcard = isset($post['idcard'])?trim($post['idcard']):'';
        $owner = isset($post['owner'])?trim($post['owner']):'';
        $phone = isset($post['phone'])?trim($post['phone']):'';
        $order_id = trim($post['orderid']);
        $transtime = intval($post['transtime']);
        $amount = intval($post['amount']);
        $currency = isset($post['currency'])?intval($post['currency']):156;
        $product_catalog = isset($post['productcatalog'])?trim($post['productcatalog']):1;
        $product_name = trim($post['productname']);
        $product_desc = trim($post['productdesc']);
        $identity_type = isset($post['identitytype'])?intval($post['identitytype']):2;
        $identity_id = trim($post['identityid']);
        $user_ip = trim($post['userip']);
        $user_ua = isset($post['userua'])?trim($post['userua']):'';
        $terminaltype = intval($post['terminaltype']);//0IMEI,1MAC,2UUID,3OTHER
        $terminalid = trim($post['terminalid']);
        $callbackurl = !empty($post['callbackurl'])?trim($post['callbackurl']):$this->_yeepayConfig['banknotify_url'];//后台回调地址
        $fcallbackurl = trim($post['fcallbackurl']);//页面回调地//用户在网页支付成功页面，点击“返回商户”时的回调地址
        $orderexp_date = isset($post['orderexpdate'])?intval($post['orderexpdate']):24*60;
        $paytypes = isset($post['paytypes'])?trim($post['paytypes']):'';
        $version = isset($post['version'])?trim($post['version']):'';
        $is_pc = isset($post['is_pc'])?trim($post['is_pc']):0;
        
        $url = $yeepay->webPay($order_id, $transtime, $amount, $cardno, $idcardtype, $idcard, $owner, $product_catalog, $identity_id, $identity_type, $user_ip, $user_ua, $callbackurl, $fcallbackurl, $currency, $product_name, $product_desc, $terminaltype, $terminalid, $orderexp_date, $paytypes, $version,$is_pc);
        /*if (array_key_exists('error_code', $url)) {
            return;
        } else {
            $arr = explode("&", $url);
            $encrypt = explode("=", $arr[1]);
            $data = explode("=", $arr[2]);
            header('Location:' . $url);
        }*/
        //https://ok.yeepay.com/paymobile/api/pay/request?merchantaccount=10013760108&encryptkey=U2SrQE6%2B4lC7n5mkV7xm2rIuI%2FSKUUTLqbik7zavQI15BtVmRBo4pS2e9EVQX6tDfOTntiCYaffqQV9THTfB4QFfDbqILnJWeKu3%2BbPol6%2F5QqqsbAs%2FHadWY%2F75r%2F%2FLyWCTVwA8cz8wnsmn7HwZdAm3qAtkjxudrlAU4qAljmM%3D&data=iD5syoGlRveD80vn4x4Eaot724%2FKobzrYEjI318nMmYHXQu008jtpTjCfFF932WwyOIA%2Fl6qLGdZClDxxmCsHXjNrldmURvNI4Ca7sfZMhMpOiRo0bEOPBgXJg5rKWVOl%2FQtm4hFnBodowS2y04VS0vJoDwM19SuHSabBBZ9EU1Nh2Ic6LdEUsv%2FCl6VQlQAC9PyIMw0NslXbbzeuHp16%2BdXQep5QX%2FhCFlKdEn5%2Bkdt0%2FUY5Z8ZUsDZYvxm0a3cj0DyKy1%2F5LdkGZFYKQA%2Bca%2FKIBiQK7yoGcqcC%2FFxux5%2FdILCQpZ%2FAQ9ogfhbr5%2ByAQ%2BM70UViCpgUas84A4us5u20RQTx5W6JQ93ULXMwrWDpls3aa2rxVIxYsbtBiUKCQJDH0wqnNTH9DBTFaZH4fEPV67aF0Pxyacx%2FSAQg5bky6ybWBDVCfY1drBFZkyS1CGpiS01%2FI9%2FntOE7gmo8ziCjLwuxhRbAJcM%2FjZc9OEgKRWy0IT0mfZyf3WMKSKF8973K6VaTDlVZU96T9rauUvd%2BAxl9goRC%2FV5g0i7uIDmV%2ByG8kRAx9u0%2FoZ%2FyfVem%2Fpo3ELfgPOQbgbcvpBgbgOCZ0OHekBrUiaXa%2F7AoQsZQlST87SPoHfZAGnaB%2Bqqo1Ra7XWdEag2A2sV5J2%2BqCN%2B54%2FTaDpF62jJhKc0wm%2BylMyS9Bg81e9GxNO9Y60pTw7LBgAXgSQwIfU0bKElpTu23Fss30Njd1U%2B6Yja0LyQPXYIpVQLaNfefaWutKQxLr1jBtwiFKa7nj4hcz3Q2WtfzD3iUXgijoRifCMu8zLmJj9FxfzLfv%2BMwoQtO0kka4lzrBf9RiBClblcyQGwxyNpNSXuFL%2BkhJVs5ZJiBol1eGoQ0WIUZ8v%2FN7oijy6E
        return $url;
    }
    
    //易宝支付银行卡回调数据(银行卡)
    public function yeepayCallbackApi($post) {
        include_once "yeepay_bank/yeepay/yeepayMPay.php";
        $merchantaccount=$this->_yeepayConfig['merchantId'];
        $merchantPublicKey=$this->_yeepayConfig['merchant_publickey'];
        $merchantPrivateKey=$this->_yeepayConfig['merchant_privatekey'];
        $yeepayPublicKey=$this->_yeepayConfig['yeepay_publickey'];
        $yeepay = new \yeepayMPay($merchantaccount, $merchantPublicKey, $merchantPrivateKey, $yeepayPublicKey);
	$return = $yeepay->callback($post['data'], $post['encryptkey']);
        //echo "success";
        return $return;
        /*[merchantaccount] => 10013760108
        [cardtype] => 2
        [amount] => 1
        [status] => 1
        [bankcode] => CMBCHINA
        [bank] => 招商银行
        [orderid] => 2016082715293614722829766184857
        [yborderid] => 411608275578514903
        [lastno] => 6422
        [sign]=>sssss
         */
    }
      
    ###################################################################################################
    #########################################applepay##################################################
    ###################################################################################################
    //苹果支付回调地址(验证支付)
    public function applepayNotifyUrl($post) {
        $isSandbox=isset($this->_applepayConfig['sandbox'])?$this->_applepayConfig['sandbox']:true;    
        $postfields=json_encode(['receipt-data'=>$post['receipt_data']]);
        $url='https://buy.itunes.apple.com/verifyReceipt';        
        $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($url,$postfields);
        if(isset($result['status']) && $result['status']==21007){
            $url='https://sandbox.itunes.apple.com/verifyReceipt';
            $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($url,$postfields);
            return $result;
            
        }
        return $result;
    }
    
    
    
    //数组转换xml
    public function arrayToXml($arr){ 
        $xml = "<xml>"; 
        foreach ($arr as $key=>$val){ 
            if(is_array($val)){ 
                $xml.="<".$key.">".arrayToXml($val)."</".$key.">"; 
            }else{ 
                $xml.="<".$key.">".$val."</".$key.">"; 
            } 
        } 
        $xml.="</xml>"; 
        return $xml; 
    }

    //测试方法
    public function logResult($word=''){
        $fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
    }
}
