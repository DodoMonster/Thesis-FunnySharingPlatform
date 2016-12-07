<?php
	include "TopSdk.php";
	date_default_timezone_set('Asia/Shanghai'); 

/*	$httpdns = new HttpdnsGetRequest;
	$client = new ClusterTopClient("appkey","appscret");
	$client->gatewayUrl = "http://gw.api.taobao.com/router/rest";
        //测试：http://gw.api.tbsandbox.com/router/rest
	var_dump($client->execute($httpdns));
*/
        //App Key： 23260048
        //App Secret： 6e80f1826471f20bd6cc181dac3e055e
$c = new TopClient;
$c->appkey = 23260048;
$c->secretKey = '6e80f1826471f20bd6cc181dac3e055e';
$req = new TradeFullinfoGetRequest;
$req->setFields("tid,type,status,payment,orders");
$req->setTid("123456789");
$resp = $c->execute($req, $sessionKey);
print_r($resp);