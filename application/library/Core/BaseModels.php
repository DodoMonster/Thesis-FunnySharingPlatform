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
    
  
}

