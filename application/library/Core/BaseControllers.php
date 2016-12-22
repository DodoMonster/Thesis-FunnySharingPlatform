<?php
namespace Core;
class BaseControllers extends \Yaf_Controller_Abstract{
    protected $_apiWhiteModelList=array('Oauth','Tests'); // 白名单模块TODO写入配置文件
    protected $_postData=array();//post数据
    protected $_getData=array();//get数据
    protected $_paramData=array();//路由数据
    protected $_mid=0;//当前登录用户uid
    protected $_uid=0;//当前登录admin用户uid
    protected $_uidLevel=0;//当前登录admin用户权限
    protected $_count=20;//默认个数
    protected $_page=1;//默认页数
    protected $_userAgent='';
    protected $_httpUserAgent='';
    protected $_httpReferer='';
    protected $_remoteIp='';
    protected $_realIp='';
    protected $_channelId='';//渠道
    protected $_module='';
    protected $_controller='';
    protected $_action='';
    protected $_returnFormat='json';
    protected $_sysVersion=0;//默认版本
    //protected $_sessionObject=null; //session对象
    protected $_isAjax = FALSE;
     
    protected $_clientId='';
    protected $_clientSecret='';
    protected $_oauthToken='';
    protected $_gameInfo=array();

    //初始化系统配置
    protected function init() {
        $this->fitlerHTTPValue();
        $this->_httpReferer=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
        $this->_remoteIp=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
        $this->_realIp=$this->getRealIp();
        //TODO跨域请求设置
        //$this->_isAjax=$this->getRequest()->isXmlHttpRequest();
        $this->_count=intval(isset($this->_postData['count'])>0?$this->_postData['count']:(isset($this->_getData['count'])>0?$this->_getData['count']:20));
        $this->_page=intval(isset($this->_postData['page'])>0?$this->_postData['page']:(isset($this->_getData['page'])>0?$this->_getData['page']:1));
     
        // $this->fitlerUserAgent();
        // $this->isDebug();
        $this->fitlerSession();
        print_r($this->_uid);
        print_r($this->_mid);

    }
    
    //参数过滤
    private function fitlerHTTPValue(){
        $postData=$this->getRequest()->getPost();
        if(!empty($postData)){
            foreach ($postData as $k=>$v){
                if(!is_array($postData[$k])){
                    $this->_postData[$k]= filter_input(INPUT_POST,$k);                    
                }else{
                    $this->_postData[$k]=filter_input(INPUT_POST,$k,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
                }
            }
        }
        $getData=$this->getRequest()->getQuery();
        if(!empty($getData)){
            foreach ($getData as $k=>$v){
                if(!is_array($getData[$k])){
                    $this->_getData[$k]=filter_input(INPUT_GET,$k);
                }else{
                    $this->_getData[$k]=filter_input(INPUT_GET,$k,FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
                }
            }
        }
        $this->_paramData=$this->getRequest()->getParams();
    }
    
    //规范用户代理
    private function fitlerUserAgent(){
        //被禁止的用户代理
        $banUserAgent=['Sogou web spider','Dalvik'];   
        //规范用户代理模式
        if(isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])){
            
            $httpUserAgent=$_SERVER['HTTP_USER_AGENT'];   
            foreach ($banUserAgent as $k=>$v){
                if(strpos($httpUserAgent,$v)){
                    exit(0);
                }
            }
            if(strpos($httpUserAgent,'iPhone')!==false || strpos($httpUserAgent,'iOS')!==false){
                $userAgent='ios';
            }elseif(strpos($httpUserAgent,'Android')!==false){
                $userAgent='android';
            }else{
                //排除抓取可能性
                $userAgent='pc';//strpos($httpUserAgent,'Mozilla')
            }
        }else{
            if($this->_module=='admin'){
                $_SERVER['HTTP_USER_AGENT']='admin';
                $userAgent='pc';
            }elseif($this->_module=='api' && $this->_controller=='stat'){
                $_SERVER['HTTP_USER_AGENT']='stat';
                $userAgent='pc';
            }else{
                BaseErrors::ErrorHandler(5005);
            }
        }
        $this->_userAgent= $userAgent;
        $this->_httpUserAgent= $_SERVER['HTTP_USER_AGENT'];
    }
    
    //过滤会话//设置mid,uid
    private function fitlerSession(){
        //TODO,COOKIE绑定登录记录
        //TODO有session的过滤方案(多次请求问题，恶意抓取问题) 
        if(isset($_SESSION[SESSION_LOGGED_USERID]) && $_SESSION[SESSION_LOGGED_USERID]>0){
            print_r('a');
            if(isset($_COOKIE[COOKIE_LOGGED_USER])&&!empty($_COOKIE[COOKIE_LOGGED_USER])){
                //$tmpValue=explode('#',filter_input(INPUT_COOKIE, 'LOGGED_USER'));
                print_r('b');
                $tmpValue=explode('#',$_COOKIE[COOKIE_LOGGED_USER]);
                $loggedUser=isset($tmpValue[1])?base64_decode($tmpValue[1]):0;
                if($loggedUser==$_SESSION[SESSION_LOGGED_USERID]){
                    $this->_mid=$_SESSION[SESSION_LOGGED_USERID];
                }elseif($loggedUser>0){
                    $_SESSION[SESSION_LOGGED_USERID]=$loggedUser;
                    $this->_mid=$_SESSION[SESSION_LOGGED_USERID];
                }else{
                    BaseErrors::ErrorHandler(5001);
                }
            }else{
                print_r('c');
                $this->_mid=$_SESSION[SESSION_LOGGED_USERID];
            }
            return;
        }else{
            print_r('d');
            if(isset($_COOKIE[COOKIE_LOGGED_USER])&&!empty($_COOKIE[COOKIE_LOGGED_USER])){
                print_r('e');
                $tmpValue=explode('#',$_COOKIE[COOKIE_LOGGED_USER]);
                $loggedUser=isset($tmpValue[1])?base64_decode($tmpValue[1]):0;
                if($loggedUser>0){
                    print_r('g');
                    $_SESSION[SESSION_LOGGED_USERID]=$loggedUser;
                    $this->_mid=$_SESSION[SESSION_LOGGED_USERID];
                    return;
                }else{
                    print_r('f');
                    BaseErrors::ErrorHandler(5001);
                }
            }
        }

        if(isset($_SESSION[SESSION_LOGGED_ADMIN_USERID]) && $_SESSION[SESSION_LOGGED_ADMIN_USERID]>0){
            $this->_uid=$_SESSION[SESSION_LOGGED_ADMIN_USERID];
        }        
    }
    
    
    //设置授权管理员会话
    protected function setOauthAdminSession($data){ 
        if($data['code']==200 && isset($data['data']['user_id']) && $data['data']['user_id']>0){
            session_regenerate_id();
            $_SESSION=[];
            $_SESSION[SESSION_LOGGED_USERID] = $data['data']['user_id'];
        }        
    }
    
    //取消授权管理员会话
    protected function unsetOauthAdminSession(){
        if(filter_has_var(INPUT_COOKIE, session_name())){
            setcookie(session_name(),'',time() - 3600,'/');
        }
        $_SESSION=array();
        session_destroy();
    }

    //开启调试
    protected function isDebug(){
        if(ENVIRONMENT==0){
            $this->_returnFormat=isset($this->_postData['format'])?$this->_postData['format']:(isset($this->_getData['format'])?$this->_getData['format']:'json');
            $userAgent=isset($this->_postData['userAgent'])?strtolower($this->_postData['userAgent']):(isset($this->_getData['userAgent'])?strtolower($this->_getData['userAgent']):'');
            if(!empty($userAgent) && in_array($userAgent,array('android','ios'))){
                $this->_userAgent=$userAgent;
            }
        }
    }

     //返回数据
    protected function returnValue($data){
        if(!isset($data['code']) || !isset($data['message']) ||!isset($data['data'])){
            BaseErrors::ErrorHandler(5000);
        }
        if($this->_returnFormat==='json'){
            header("Access-Control-Allow-Origin: http://webdzz.91sd.com");
            header('Access-Control-Allow-Credentials: true');
            header("Content-Type: application/json;charset=utf-8");
            echo json_encode($data);
        }elseif($this->_returnFormat==='test'){
            print_r($data);
        }else{
            BaseErrors::ErrorHandler(5000);
        }
        exit;
    }

    //生成签名
    protected function generateSign($params) {
        $secretKey=isset($params['client_secret'])?$params['client_secret']:'';
        if(empty($secretKey)){
            BaseErrors::ErrorHandler(5007);
        }
        unset($params['client_secret']);
        ksort($params);
        $stringToBeSigned = $secretKey;
        foreach ($params as $k => $v) {           
            if(is_array($v)){//$v is a array
                $v=  implode(',', $v);
            }
            if ("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $secretKey;
        return strtoupper(md5($stringToBeSigned));
    }
    
    //验证签名
    protected function verifySign($clientSecret=''){
        if(ALLOW_SIGN){
            $sysSign=isset($this->_postData['sys_sign']) ? $this->_postData['sys_sign']: '';
            $params=$this->_postData;
            $params['client_secret']= $clientSecret;
            unset($params['sys_sign']);
            if(isset($this->_postData['timestamp'])&&($this->_postData['timestamp']<time()-300)){
                //BaseErrors::ErrorHandler(5004,'Timestamp Invalid');
            }
            $sysSign1=$this->generateSign($params);
            if($sysSign!==$sysSign1){
                BaseErrors::ErrorHandler(5004);
            }
            unset($this->_postData['timestamp']);
        }
    }
    
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
    
    //获得浏览器(no used)
    function getBrowser(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Maxthon')) {
            $browser = 'Maxthon';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 12.0')) {
            $browser = 'IE12.0';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 11.0')) {
            $browser = 'IE11.0';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0')) {
            $browser = 'IE10.0';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
            $browser = 'IE9.0';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
            $browser = 'IE8.0';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
            $browser = 'IE7.0';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
            $browser = 'IE6.0';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'NetCaptor')) {
            $browser = 'NetCaptor';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
            $browser = 'Netscape';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx')) {
            $browser = 'Lynx';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
            $browser = 'Opera';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
            $browser = 'Google';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
            $browser = 'Firefox';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
            $browser = 'Safari';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'iphone') || strpos($_SERVER['HTTP_USER_AGENT'], 'ipod')) {
            $browser = 'iphone';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
            $browser = 'iphone';
        } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'android')) {
            $browser = 'android';
        } else {
            $browser = 'other';
        }
        return $browser;
    }

    //验证签名
    protected function verifySign1($clientSecret=''){
        $sysSign=isset($this->_postData['sys_sign']) ? $this->_postData['sys_sign']: '';
        $params=$this->_postData;
        $params['client_secret']= $clientSecret;
        unset($params['sys_sign']);
        if(isset($this->_postData['timestamp'])&&($this->_postData['timestamp']<time()-300)){
            //BaseErrors::ErrorHandler(5004,'Timestamp Invalid');
        }
        $sysSign1=$this->generateSign($params);
        if($sysSign!==$sysSign1){
            BaseErrors::ErrorHandler(5004);
        }
        unset($this->_postData['timestamp']);
    }
}

