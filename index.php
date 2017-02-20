<?php
    define("SITE_PATH", realpath(dirname(__FILE__)));
    define("APPLICATION_PATH", SITE_PATH."/application");
    require_once APPLICATION_PATH.'/library/Core/BaseApplication.php';
    date_default_timezone_set('Asia/Shanghai');
    $app=new BaseApplication();
    // ini_set("display_errors", 1);
    // error_reporting(E_ALL | E_STRICT);
    $app->start();

   
    

 	//define("SITE_PATH", realpath(dirname(__FILE__)));
	//define("APPLICATION_PATH",  SITE_PATH."/application");  指向public的上一级 
	//$app  = new Yaf_Application(APPLICATION_PATH . "/../conf/application.ini");
	//$app->run();
