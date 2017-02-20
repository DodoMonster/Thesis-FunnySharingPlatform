<?php
    define("SITE_PATH", realpath(dirname(__FILE__)));
    define("APPLICATION_PATH", SITE_PATH."/application");
    require_once APPLICATION_PATH.'/library/Core/BaseApplication.php';
    $app=new BaseApplication();
    date_default_timezone_set('Asia/Shanghai');
    $app->startc();