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

        \Yaf_Registry::set("dbconfig",new \Yaf_Config_Ini('conf/db.ini', 'development'));
        \Yaf_Registry::set("redisconfig",new \Yaf_Config_Ini('conf/redis.ini', 'development'));
        //以用户访问为主设定
        $siteUrl = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $sysSiteUrl);
        $siteUrl='http://'.$siteUrl;
        define("SITE_URL", $siteUrl);
        preg_match('/[^.]+\.[^.]+$/',$siteUrl, $matches);
        

        session_start(); 
        
        define('RESOURCE_PATH',SITE_URL.\Yaf_Registry::get("sysconfig")->sys->resource);
        define('ALLOW_SIGN',\Yaf_Registry::get("sysconfig")->sys->allow->sign);
        define('DEBUG_ERROR',\Yaf_Registry::get("sysconfig")->debug->error);
        define('DEBUG_DB',\Yaf_Registry::get("sysconfig")->debug->db);
        define('COOKIE_LOGGED_USER','FTY_LOGGED_USER');
        define('SESSION_LOGGED_USERID','uid');
 
        //添加配置文件中的路由
        $router = $dispatcher->getRouter();
        $routerConfig=\Yaf_Registry::get("sysconfig")->routes->rewrite;
        $router->addConfig($routerConfig);

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
