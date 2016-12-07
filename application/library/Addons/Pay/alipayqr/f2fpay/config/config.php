<?php
$config = array (	
		//支付宝公钥
		'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB",

		//商户私钥
		'merchant_private_key' => "MIICXAIBAAKBgQC1BxMoWnUHdjG9cziVkFY7n4a1vbhC2Jne742Dh7FFK/P6Er/yvW/ooiivly/QaNh35C8Cd38W/sy1GKvXz9FdN1bYVzzRhBIGDEK5sXyDY1GH9S9tT+BTqSh4WkTCtfBwbl59tbm0AsyDzBvsg7wLH2slq0h11LLIpb/ssvBl8QIDAQABAoGAL/MKyQKEwxA0hpjRYRRVMv/DP7pb5yDWSO2szy0O8GJ/AjsbKqGw1a62FiR2nr5WsBL5vK6INEATWtiJE+XQ72zekL4Yr2M1Ku7DrsmM91kR5/8Y9erNbH2nHMJjG+zmDLGV7Vl2bS/54G2bk6H0jK3mswrkXRLUNmb9vp0SK10CQQDaJvgS9Bbp+1aMEDdIDxVJt4YQCXTB8oMolUNmeadq6h/9FYbz62gp2SniUUHsTGtSNCeLpuVQbtdS1f/pZCWvAkEA1G9AVhPw2/E5oDg1YfvRUedEnL0/3I6Z2OcsJAi3c/5IbOGSJQPOE2TAsggdftHn/4rGAEHmED5SDI1CFsS2XwJATVa3Z/DFFtqWCj3Ne2xtfZ7kllbj38ZcHU0dgfzrD4pFIPI6z8rLONrWcHeiSWWmPpRi2K2oamWBTMq1WUa53QJAVfQRrwJwgT2QWHCFwPiX0DDoBRdZwI7+VtHqfUX9nHQ1r8BxVoK8ngI1KZO5A3hBC5eF19LSGJ5uql95byb2twJBAJJHkHwq+IoeebFsHxCmNLOHKt2daiwwIMeUCRaL7bQyVAuyAltBhvTm1AEN58xGQUfiXp1CZlviG/ynLCe7iV4=",

		//编码格式
		'charset' => "UTF-8",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//应用ID
		'app_id' => "2016021401143769",

		//异步通知地址,只有扫码支付预下单可用
		'notify_url' => "http://api.91sd.com/api/pay/alipayNotifyUrlChenyou",

		//最大查询重试次数
		'MaxQueryRetry' => "10",

		//查询间隔
		'QueryDuration' => "3"
);