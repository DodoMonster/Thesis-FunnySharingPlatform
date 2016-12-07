<?php
namespace Core;
class BaseModels{
    private $_dbConfig=array();
    private $_dbName='';
    private $_dbConnection='';
    private $_nosqlConfig=array();
    protected $db=null;
    protected $cache=null;
    protected $nosql=null;
    
    //TODO
    //可以正常执行，没找到也可以正常往下执行
    //$this->find($options);$this->db->find($options);
    /*public function __call($name, $arguments) {
        
    }*/
    
    public function __construct($config=array(),$name = '', $connection = '') {
        $this->_dbConfig = !empty($config) ? $config : \Yaf_Registry::get("dbconfig")->db->toArray();
        $this->_dbName = !empty($name) ? $name : '';
        $this->_dbConnection = !empty($connection) ? $connection : '';
        $this->initDb($this->_dbConfig,$this->_dbName,$this->_dbConnection);
        $this->_nosqlConfig =\Yaf_Registry::get("redisconfig")->nosql->toArray();
        $this->initNosql($this->_nosqlConfig);
    }
    
    public function initDb($config,$name = '', $connection = ''){
        if($this->db == NULL){
            $this->db= new \Core\Dao\Db\DbInit($config,$name,$connection);
        }
        return $this->db;
    }
    
    public function initCache(){
        
    }
    
    public function initNosql($nosqlConfig){
        if($this->nosql == NULL){
            $this->nosql= new \Core\Dao\Nosql\RedisInit($nosqlConfig);
        }
        return $this->nosql;
    }
    
    //TODO,外部库的错误返回（统一集成到model方法中调用，不在控制器中直接调用外部库）
    //返回结果
    public function returnResult($code,$data='',$message=''){
        switch ($code){
            case 200:
                $data=!empty($data)?$data:new \stdClass();
                $message='Success';
                break;
            case 201:
                $data=!empty($data)?$data:array('totalPage'=>0,'count'=>0,'page'=>1,'list'=>array());
                $message='Not Have Any Data';
                break;
            case 202:
                $data=!empty($data)?$data:new \stdClass();
                $message='Already Done';
                break;
            case 203:
                $data=!empty($data)?$data:new \stdClass();
                $message='';//不跳出的错误
                break;
            case 400:
                $data=!empty($data)?$data:new \stdClass();
                $message='';//不跳出的错误
                break;
            case 401:
                $data=!empty($data)?$data:new \stdClass();
                $message='';//不跳出的错误
                break;
            case 402:
                $data=!empty($data)?$data:new \stdClass();
                $message='';//不跳出的错误
                break;
            default :
                \Core\BaseErrors::ErrorHandler($code,$data,$message);
                break;
        }
        return array('code'=>$code,'message'=>$message,'data'=>$data);
    }        
    
    //验证是否是手机号码
    public function isRegCellphone($cellphone){
        //134—139、150—152、158、159、182,130—132、155、156,147、157、188,186,133、153,189、180、181,178，177，176
        $status=preg_match('/^1[34578]\d{9}$/', $cellphone);
        return $status ? TRUE:FALSE;
    }
    
    //验证是否是规则用户名
    public function isRegUname($uname){
        $status=preg_match('/^[A-Za-z0-9]{3,20}$/', $uname);
        return $status ? TRUE:FALSE;
    }
    
    //验证是否是规则密码
    public function isRegPassword($password){
        $status=preg_match('/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/',$password);
        return $status ? TRUE:FALSE;
    }
    
    //验证是否是规则idfa
    public function isIDFA($idfa){
        return preg_match('/^[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}$/', $idfa);
    }
    
    //base62编码
    public function base62($i){
        if($i<0) return '';
        $ch = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $a='';
        do{
            $a=$ch[bcmod($i,62)].$a;
            $i=bcdiv($i,62,0);      
        }while($i>0);
        return $a;
    }
        
    //毫秒数
    public function getMicrotime(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec)*10000;
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
    /*    
    //计算字符串长度
    public function countWords($var){
        $count=0;
        if(mb_detect_encoding($var)=='ASCII'){
            $var=urldecode($var);
        }
        $strLen = mb_strlen($var,'UTF-8');
        for($i=0; $i<$strLen; $i++) {
            $oneChar = mb_substr($var, $i, 1,'UTF-8');
            $count+=preg_match("/^[\x7f-\xff]+$/", $oneChar)?1:0.5;	
        }
        return $count;
    }
    
    //限制字数
    public function limitWordsCount($var,$num){
        $str='';
        $count=0;
        $strLen = mb_strlen($var); 
        for($i=0; $i<$strLen; $i++) {
            if($count>=$num){break;}
            $oneChar = mb_substr($var, $i, 1);
            $count+=preg_match("/^[\x7f-\xff]+$/", $oneChar)?1:0.5;	
            $str.=$oneChar;
        }
        return $str;
    }
    
    //限制推送字数：35个中文字符+5英文字符,总长度为113
    public static function limitPushWordsCount($var){
        $message='';
        $count=0;
        $len=0;
        for($i=0;$i<strlen($var);$i++){
            if($len>=108 || $count>=36){
                break;
            }
            if(preg_match("/^[\x7f-\xff]+$/", $var[$i])){
                if(isset($var[$i])) $message.=$var[$i];
                if(isset($var[$i+1])) $message.=$var[$i+1];
                if(isset($var[$i+2])) $message.=$var[$i+2];
                //$message.=$var[$i].$var[$i+1].$var[$i+2];
                $count+=1;
                $i+=2;
                $len+=3;
            }else{
                $message.=$var[$i];
                $len+=1;
            }
        }
        if(strlen($var)>=113){
            $message.='...';
        }
        return $message;
    }
  
    //邮箱是否使用
    protected function isEmailExsit($email) {
        $is_email = preg_match('/^\w+[(\w\.?)|(\.?\w)|(\.?\w\.?)|(\w\-?)]{3,28}\w+@\w+(\-\w)*\.\w+([.]\w+)*$/', $email);
        if (!$is_email) {
            \Core\BaseErrors::EmailIllegal();
        }
        $options=array('table'=>'ts_user','field'=>'uid','where'=>array('email'=>'?'),'param'=>array($email));
        $user=$this->find($options);
        if(!empty($user)){
           \Core\BaseErrors::EmailExist();
        }
    }
    
    //邮箱是否使用
    public function isEmailNotExsit($email) {
        //$is_email = preg_match('/^\w+[(\w\.?)|(\.?\w)|(\.?\w\.?)]{3,28}\w+@\w+\.\w+([.]\w+)*$/', $email);
        $is_email = preg_match('/^\w+[(\w\.?)|(\.?\w)|(\.?\w\.?)|(\w\-?)]{3,28}\w+@\w+(\-\w)*\.\w+([.]\w+)*$/', $email);
        if (!$is_email) {
            \Core\BaseErrors::EmailIllegal();
        }
        $options=array('table'=>'ts_user','field'=>'uid','where'=>array('email'=>'?'),'param'=>array($email));
        $user=$this->find($options);
        if(!empty($user)){
            return $user['uid'];
        }else{
            \Core\BaseErrors::EmailNotExist();
        }
    }
    
    //用户名是否合法
    public function isRegularUname($uname){
        $num=$this->countWords($uname);//用户名长度是否合法
        if($num<2 || $num>15){
            \Core\BaseErrors::UnameLengthIllegal();
        }
        $options=array('table'=>'ts_user','field'=>'uid','where'=>array('uname'=>'?'),'param'=>array($uname));//用户名是否使用
        $user=$this->find($options);
        if(!empty($user)){
            \Core\BaseErrors::UnameExist();
        }
    }
    
    //邮箱是否使用
    public function isRegularEmail($email){
        //$is_email=preg_match('/^\w+[(\w\.?)|(\.?\w)|(\.?\w\.?)]{3,28}\w+@\w+\.\w+([.]\w+)*$/', $email);
        $is_email = preg_match('/^\w+[(\w\.?)|(\.?\w)|(\.?\w\.?)|(\w\-?)]{3,28}\w+@\w+(\-\w)*\.\w+([.]\w+)*$/', $email);
        if(!$is_email){
            \Core\BaseErrors::EmailIllegal();
        }
        $options=array('table'=>'ts_user','field'=>'uid','where'=>array('email'=>'?'),'param'=>array($email));
        $user=$this->find($options);
        if(!empty($user)){
            \Core\BaseErrors::EmailExist();
        }
    }    
    */
}

