<?php
include_once 'BaseErrors.php';
class BaseApplication {
    private $__application='';
    private $__dispatcher='';

    private function _initConfig(){
        error_reporting(0);
        register_shutdown_function(array('Core\BaseErrors','fatalError'));
        set_error_handler(array('Core\BaseErrors','appError'));
        set_exception_handler(array('Core\BaseErrors','appException'));
                
        $this->__application = new \Yaf_Application(SITE_PATH . "/conf/application.ini");
        $dispatcher= $this->__application->getDispatcher();
        \Yaf_Registry::set("sysconfig",\Yaf_Application::app()->getConfig());
        define('SESSION_REDIS',\Yaf_Registry::get("sysconfig")->sys->session->redis);
        define('SESSION_TIME',\Yaf_Registry::get("sysconfig")->sys->session->time);
        define('ENVIRONMENT',\Yaf_Registry::get("sysconfig")->sys->environment);

        \Yaf_Registry::set("dbconfig",new \Yaf_Config_Ini('conf/db.ini', 'development'));
        \Yaf_Registry::set("redisconfig",new \Yaf_Config_Ini('conf/redis.ini', 'development'));
        \Yaf_Registry::set("payconfig",new \Yaf_Config_Ini('conf/pay.ini', 'development'));
        //以用户访问为主设定
        $siteUrl = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $sysSiteUrl);
        $siteUrl='http://'.$siteUrl;
        define("SITE_URL", $siteUrl);
        preg_match('/[^.]+\.[^.]+$/',$siteUrl, $matches);
        define('DOMAIN',isset($matches[0])?$matches[0]:'');
        
        if(SESSION_REDIS){
            $host=\Yaf_Registry::get("redisconfig")->nosql->HOST;
            $auth=\Yaf_Registry::get("redisconfig")->nosql->AUTH;
            if(!empty($auth)){
                $link="tcp://$host:6379?auth=$auth";
            }else{
                $link="tcp://$host:6379";
            }
            ini_set('session.save_handler','redis');
            ini_set('session.save_path',$link);
            ini_set('session.cookie_domain', '.'.DOMAIN);
            ini_set('session.gc_divisor',3000);
            ini_set('session.gc_maxlifetime',SESSION_TIME);
            ini_set('session.cookie_lifetime',SESSION_TIME);
        }else{
            ini_set('session.gc_maxlifetime', SESSION_TIME);
            ini_set('session.cookie_lifetime',SESSION_TIME);
        }
        session_start(); 
        
        define('RESOURCE_PATH',SITE_URL.\Yaf_Registry::get("sysconfig")->sys->resource);
        // define('REAL_RESOURCE_URL',\Yaf_Registry::get("sysconfig")->sys->url->resource);
        // define('CDN_URL',\Yaf_Registry::get("sysconfig")->sys->url->cdn);
        define('ALLOW_SIGN',\Yaf_Registry::get("sysconfig")->sys->allow->sign);
        define('ALLOW_OAUTH2',\Yaf_Registry::get("sysconfig")->sys->allow->oauth2);
        define('DEBUG_ERROR',\Yaf_Registry::get("sysconfig")->debug->error);
        define('DEBUG_DB',\Yaf_Registry::get("sysconfig")->debug->db);
        define('COOKIE_LOGGED_USER','FTY_LOGGED_USER');
        define('SESSION_LOGGED_USERID','uid');
        define('SESSION_LOGGED_EMAIL','email');
        define('SESSION_LOGGED_CELLPHONE','cellphone');
        define('SESSION_LOGGED_COMPANYID','cid');
        define('SESSION_LOGGED_ADMIN_USERID','admin_uid');
        define('SESSION_LOGGED_ADMIN_PRIVILEGE','admin_level');
 
        //添加配置文件中的路由
        $router = $dispatcher->getRouter();
        $routerConfig=\Yaf_Registry::get("sysconfig")->routes->rewrite;
        $router->addConfig($routerConfig);
      
        //自定义路由
       /*$routeConfig = array(
            "home" => array("type"  => "rewrite","match" => "/home", "route" => array('module'=>'web','controller' => "index",'action'=>"home")),
            "articles-detail"=>array("type"  => "rewrite","match" => "/article/:id", "route" => array('module'=>'web','controller' => "index",'action'=>"articlesDetail"))           
        );
       print_r($routeConfig);
        //web/index/accessOauth第三方回调页
        $router->addConfig(new Yaf_Config_Simple($routeConfig));*/

        $dispatcher->autoRender(FALSE);//关闭自动渲染
        //初始化视图路径
        $dispatcher->initView(SITE_PATH . "/public/default/views");
    }
    
    //基本请求
    public function start(){
        $this->_initConfig();
        $this->__application->run();
    }
    
    //命令行请求
    public function startc(){
        $this->_initConfig();
        $this->__application->getDispatcher()->dispatch(new \Yaf_Request_Simple());
    }   
}
