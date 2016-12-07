<?php

/*
 * @Description 易宝支付非银行卡支付专业版接口范例 
 * @V3.0
 * @Author yang.xu
 */

/* 商户编号p1_MerId,以及密钥merchantKey 需要从易宝支付平台获得 */
//p1_MerId = "10001126856";                           #测试使用
//$merchantKey = "69cl522AV6q613Ii4W6u8K6XuW8vM1N6bFgyv769220IuYe9u37N4y7rI4Pl";   #测试使用
$p1_MerId = "10013423030";                           #测试使用
$merchantKey = "1d42g88F007o5UY64fS1X1eFW347omx72D0K9908954Ay98k2Xf0vqe7zq4x";   #测试使用
$logName = "YeePay_CARD.log";
# 非银行卡支付专业版请求地址,无需更改.
$reqURL_SNDApro = "https://www.yeepay.com/app-merchant-proxy/command.action";
?> 

