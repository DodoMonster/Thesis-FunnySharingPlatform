<?php
namespace Addons\Alibaba;
include "taobao/TopSdk.php";
class TaobaoUtil {
    
    private $_appkey = 23260942;//dazhuanet
    private $_secretKey = 'de6c220529782ee2d4f7a71d7ec5e20e';
    //private $_appkey = 23248010;
    //private $_secretKey ='3a59176b7e87a2cd48b4d673c3bc17cf';
    private $_sessionKey ='6101304e3f791ff5625d2af8d6b7c03fcdd841dae8f66672662785200';
    //sandbox_shenma
    //https://oauth.taobao.com/oauth2?view=web#access_token=610222583cced9fa55c54ea7eb0e6b863109b7702bb1128140203403&token_type=Bearer&expires_in=86400&refresh_token=61024255e03dfeff5c94723b01436d5c012d26eb052ac7a140203403&re_expires_in=86400&r1_expires_in=86400&r2_expires_in=86400&taobao_user_nick=jeffchein&w1_expires_in=86400&w2_expires_in=1800&state=1212&top_sign=1752D0D4A6B83D0659665343B58CE673
    //session: 610222583cced9fa55c54ea7eb0e6b863109b7702bb1128140203403
    //refresh_token: 61024255e03dfeff5c94723b01436d5c012d26eb052ac7a140203403
    
    //https://oauth.taobao.com/oauth2?view=web#access_token=6101304e3f791ff5625d2af8d6b7c03fcdd841dae8f66672662785200&token_type=Bearer&expires_in=86400&refresh_token=6100f042f2551d94ee2028281b88fe1294c6189848390642662785200&re_expires_in=86400&r1_expires_in=86400&r2_expires_in=86400&taobao_user_nick=dazhuanet&w1_expires_in=86400&w2_expires_in=1800&state=1212&top_sign=E9EDBCC69461CA7D95128B3B125C22B4
    //session: 6101304e3f791ff5625d2af8d6b7c03fcdd841dae8f66672662785200
    //refresh_token: 6100f042f2551d94ee2028281b88fe1294c6189848390642662785200
    //855652411300334
    //462503790690334
    
    //{"trade_fullinfo_get_response":{"trade":{"adjust_fee":"0.00","buyer_nick":"jeffchein","buyer_obtain_point_fee":1026,"buyer_rate":true,"cod_fee":"0.00","cod_status":"NEW_CREATED","consign_time":"2013-12-08 09:06:13","created":"2013-11-23 09:10:36","discount_fee":"0.00","end_time":"2013-12-11 09:01:06","modified":"2013-12-18 20:12:33","num":1,"num_iid":36131555094,"orders":{"order":[{"adjust_fee":"0.00","buyer_rate":true,"discount_fee":"0.00","num":1,"num_iid":36131555094,"oid":462503790690334,"outer_iid":"20700066","payment":"108.00","pic_path":"http:\/\/img01.taobaocdn.com\/bao\/uploaded\/i1\/12746031042142706\/T1n.LqXeFvXXXXXXXX_!!0-item_pic.jpg","price":"108.00","refund_status":"NO_REFUND","seller_rate":true,"seller_type":"B","status":"TRADE_FINISHED","title":"【喵鲜生】联想控股 佳沃出品 智利进口蓝莓125g*4盒 顺丰包邮","total_fee":"108.00"}]},"pay_time":"2013-11-23 09:11:04","payment":"108.00","pic_path":"http:\/\/img01.taobaocdn.com\/bao\/uploaded\/i1\/12746031042142706\/T1n.LqXeFvXXXXXXXX_!!0-item_pic.jpg","point_fee":350,"post_fee":"0.00","price":"108.00","real_point_fee":350,"received_payment":"108.00","receiver_address":"小谷围街道外环东路232号数字家庭基地13栋 A320","receiver_city":"广州市","receiver_district":"番禺区","receiver_mobile":"15602296677","receiver_name":"陈先生","receiver_phone":"","receiver_state":"广东省","receiver_zip":"511400","seller_nick":"joyvio旗舰店","seller_rate":true,"shipping_type":"express","sid":"462503790690334","status":"TRADE_FINISHED","tid":462503790690334,"title":"佳沃旗舰店","total_fee":"108.00","type":"step"},"request_id":"11if6uj2pwp30"}}
    
    /*
     * $c = new TopClient;
$c->appkey = appkey;
$c->secretKey = secret;
$req = new TradeFullinfoGetRequest;
$req->setFields("seller_nick,buyer_nick,title,type,created,sid,tid,seller_rate,buyer_rate,status,payment,discount_fee,adjust_fee,post_fee,total_fee,pay_time,end_time,modified,consign_time,buyer_obtain_point_fee,point_fee,real_point_fee,received_payment,commission_fee,pic_path,num_iid,num_iid,num,price,cod_fee,cod_status,shipping_type,receiver_name,receiver_state,receiver_city,receiver_district,receiver_address,receiver_zip,receiver_mobile,receiver_phone,orders.title,orders.pic_path,orders.price,orders.num,orders.iid,orders.num_iid,orders.sku_id,orders.refund_status,orders.status,orders.oid,orders.total_fee,orders.payment,orders.discount_fee,orders.adjust_fee,orders.sku_properties_name,orders.item_meal_name,orders.buyer_rate,orders.seller_rate,orders.outer_iid,orders.outer_sku_id,orders.refund_id,orders.seller_type");
$req->setTid(462503790690334);
$resp = $c->execute($req, $sessionKey);
     */
    
    //授权登录
    public static function oauthLogin(){
        /*测试时，需把test参数换成自己应用对应的值*/
        $url = 'https://oauth.taobao.com/token';
        $postfields= array('grant_type'=>'authorization_code',
        'client_id'=>'test',
        'client_secret'=>'test',
        'code'=>'test',
        'redirect_uri'=>'http://www.test.com');

        $post_data = '';
        foreach($postfields as $key=>$value){
            $post_data .="$key=".urlencode($value)."&";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);
        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post_data,0,-1));
        $output = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $httpStatusCode;
        curl_close($ch);
        var_dump($output);
    }

    //获取交易详细信息
    public static function getTradeFullinfo() {
        $c = new TopClient;
        $c->appkey = $this->_appkey;
        $c->secretKey = $this->_secretKey;
        $req = new TradeFullinfoGetRequest;
        $req->setFields("tid,type,status,payment,orders");
        $req->setTid("123456789");
        $resp = $c->execute($req, $sessionKey);
        print_r($resp);
    }
}
