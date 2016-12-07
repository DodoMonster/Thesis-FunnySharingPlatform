<?php

include("yeepay/yeepayMPay.php");
include("config.php");

$yeepay = new yeepayMPay($merchantaccount, $merchantPublicKey, $merchantPrivateKey, $yeepayPublicKey);
$cardno = trim($_POST['cardno']);
$idcardtype = trim($_POST['idcardtype']);
$idcard = trim($_POST['idcard']);
$owner = trim($_POST['owner']);
$phone = trim($_POST['phone']);
$order_id = trim($_POST['orderid']);
$transtime = intval($_POST['transtime']);
$amount = intval($_POST['amount']);
$currency = intval($_POST['currency']);
$product_catalog = trim($_POST['productcatalog']);
$product_name = trim($_POST['productname']);
$product_desc = trim($_POST['productdesc']);
$identity_type = intval($_POST['identitytype']);
$identity_id = trim($_POST['identityid']);
$user_ip = trim($_POST['userip']);
$user_ua = trim($_POST['userua']);
$terminaltype = intval($_POST['terminaltype']);
$terminalid = trim($_POST['terminalid']);
$callbackurl = trim($_POST['callbackurl']);
$fcallbackurl = trim($_POST['fcallbackurl']);
$orderexp_date = intval($_POST['orderexpdate']);
$paytypes = trim($_POST['paytypes']);
$version = trim($_POST['version']);

$url = $yeepay->webPay($order_id, $transtime, $amount, $cardno, $idcardtype, $idcard, $owner, $product_catalog, $identity_id, $identity_type, $user_ip, $user_ua, $callbackurl, $fcallbackurl, $currency, $product_name, $product_desc, $terminaltype, $terminalid, $orderexp_date, $paytypes, $version);

if (array_key_exists('error_code', $url)) {
    return;
} else {
    $arr = explode("&", $url);
    $encrypt = explode("=", $arr[1]);
    $data = explode("=", $arr[2]);
    echo($url);
    header('Location:' . $url);
}
?>