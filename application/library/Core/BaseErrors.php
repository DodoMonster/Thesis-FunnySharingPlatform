<?php
namespace Core;
class BaseErrors {
    //register_shutdown_function(array('BaseApplication','fatalError'));
    //set_error_handler(array('BaseApplication','appError'));
    //set_exception_handler(array('BaseApplication','appException'));
    //$app->getDispatcher()->setErrorHandler("error_handler", E_RECOVERABLE_ERROR);
    //1不抛出异常，加入自己处理（基本异常函数处理）
    //$app->getDispatcher()->throwException(FALSE);不抛出异常，
    //$app->getDispatcher()->setErrorHandler("myErrorHandler");
    //Yaf_Dispatcher::getInstance()->setErrorHandler(array($this,"myErrorHandler"));

    //2自动捕获到Error类(yaf控制器类异常处理)(Error类捕获不到整个php项目异常)
    //Yaf_Dispatcher::getInstance()->catchException(true);自定义捕获，默认配置文件是自定义捕获。到error函数

    //3BaseError类处理
    
    //错误码
    protected $_sysError=array(5000,5001,5002,5003,5004,5005,5006,5007);

    //框架错误处理
    public static function ErrorHandler($code,$result='',$message=''){
        $data=['code'=>$code,'message'=>'','data'=>new \stdClass()];
        $data['data']=!empty($result)?$result:new \stdClass();
        switch ($code){
            case 5000:
                $data['message']='Return Value Error';//1.BaseControllers.php
                break;
            case 5001:
                $data['message']='Oauth Access Error';//1.BaseControllers.php
                $data['data']=array('ck'=>serialize(filter_input(INPUT_SERVER, 'HTTP_COOKIE')));
                break;
            case 5002:
                $data['message']='Access Db Config Error';//1.DbDriver.php
                break;
            case 5003:
                $data['message']=$message;//1.self
                break;
            case 5004:
                $data['message']='Sign Error';//1.BaseControllers.php
                break;
            case 5005:
                $data['message']='UserAgent Error';//1.BaseControllers.php
                break;
            case 5006:
                $data['message']='Db PDO Error';//1.DbDriver.php
                break;
            case 5007:
                $data['message']='Client Secret Error';//1.BaseControllers.php
                break;
            case 4300:
                $data['message']='Parameter Error';
                break;
            case 4301:
                $data['message']='Register Failed';
                break;
            case 4302:
                $data['message']='Account Not Exist';
                break;
            case 4303:
                $data['message']='Password Wrong';
                break;
            case 4304:
                $data['message']='Get User Info Failed';
                break;
            case 4305:
                $data['message']='Account Exist';
                break;
            case 4306:
                $data['message']='Bind Cellphone Failed';
                break;
            case 4307:
                $data['message']='unBind Cellphone Failed';
                break;

            case 4201:
                $data['message']='Cellphone Not Bind';
                break;
            case 4202:
            $data['message']='Cellphone Is Uname';
            break;

            case 4000:
                $data['message']=!empty($message)?$message:'Failed';
                $data['data']=!empty($message)?$message:'Failed';
                break;
            case 4001:
                $data['message']='Msgcode Error';
                break;
            case 4002:
                $data['message']='Captcha Error';
                break;
            case 4003:
                $data['message']='Already Bind Account';
                break;
            case 4004:
                $data['message']='Not Oauth Account';
                break;
            case 4005:
                $data['message']='Already Register';
                break;
            case 4006:
                $data['message']='Game Not Exist';//1.PDOOAuth2.php
                break;
            case 4007:
                $data['message']='Msgcode Beyond The Limit';
                break;
            case 4008:
                $data['message']='Is Cellphone';
                break;
            case 4009:
                $data['message']='Cannot Unbind Cellphone';
                break;
            
            case 4011:
                $data['message']='Game Account Exist';
                break;
            case 4012:
                $data['message']='Oauth Bind Failed';
                break;
            case 4013:
                $data['message']='Get Oauth User Info Failed';
                break;
            
            case 4037:
                $data['message']='Pic Upload Failed';
                break;
            case 4038:
                $data['message']='Oauth2 Failed';
                break;
            case 4039:
                $data['message']='Login Session Expires';
                break;
            case 4040:
                $data['message']='Yeepay Failed';
                break;
            case 4041:
                $data['message']='Applepay Verify Failed';
                break;
            
        }
        self::EchoError($data);
    }
    
    //输出
    private static function EchoError($data){
        /*if(!DEBUG_ERROR && in_array($data['code'],array(5003,5006))){
            self::error404();
        }*/
        $log= new \Log\LogModel();
        $log->sysError($data);
        header("Access-Control-Allow-Origin: http://webdzz.91sd.com");
        header("Content-Type: application/json;charset=utf-8");
        echo json_encode($data);
        exit(0);
    }
    
    
    //TODO退出多个模式
    //致命错误
    public static function fatalError() {
        if ($e = error_get_last()) {
            $errorStr = "[".$e['type']."] ". $e['message'] . " in " . $e['file']." on line ".$e['line'] .".";
            self::ErrorHandler(5003, $errorStr, 'Fatal Error');
        }
        exit(0);
    }
    
    //异常处理
    public  static function appException($e) {
        self::ErrorHandler(5003, $e->__toString(), 'Exception');
    }
    
    //错误处理
    public static function appError($errno, $errstr, $errfile, $errline) {
      switch ($errno) {
          case E_ERROR:
          case E_PARSE:
          case E_CORE_ERROR:
          case E_COMPILE_ERROR:
          case E_USER_ERROR:            
          case E_STRICT:
          case E_USER_WARNING:
          case E_USER_NOTICE:
          default:
            $errorStr = "[$errno] $errstr "." in ".$errfile." on line $errline .";
            break;
      }
      self::ErrorHandler(5003, $errorStr, 'Notice');
    }
    
    //404错误处理
    public static function error404(){
        header('Location: /');
    }
    
    
    //TODO统一错误码状态输出
    public function getError(){
        //echo \Yaf_Application::getLastErrorNo();
    //echo \Yaf_Application::getLastErrorMsg();
    //$test=error_get_last();
    //return array('code' => 5003, 'message' => 'Sys Error', 'data' => $test);
    //echo $test;
    //try catch
    //
        //error_reporting(0);
    /*function error_handler($errno, $errstr, $errfile, $errline) {
        var_dump(Yaf_Application::app()->getLastErrorNo());
        var_dump(Yaf_Application::app()->getLastErrorNo() == YAF_ERR_NOTFOUND_CONTROLLER);
     }*/
        
         //$app->getDispatcher()->setErrorHandler("error_handler", E_RECOVERABLE_ERROR);
    }  
}
