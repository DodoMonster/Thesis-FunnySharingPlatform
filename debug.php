<?php
    define("SITE_PATH", realpath(dirname(__FILE__)));
    define("SITE_URL", "mytest.yaf.com");
    define("APPLICATION_PATH", SITE_PATH."/application");
    $app = new Yaf_Application(SITE_PATH . "/conf/application.ini");
    //关闭自动响应, 交给rd自己输出
    $response = $app->bootstrap()->getDispatcher()->returnResponse(TRUE)->getApplication()->run();
    $response->response();