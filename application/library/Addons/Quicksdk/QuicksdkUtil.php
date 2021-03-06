<?php
namespace Addons\Quicksdk;
class QuicksdkUtil {
    public function __construct() {
        require_once 'quicksdkAsy.php';
    }
    
    public function quicksdkPayNotify($post){
        //设置测试值
        /*$_POST['nt_data'] = '@108@119@174@165@158@82@167@152@171@166@161@162@159@115@90@106@94@103@83@85@154@166@156@161@155@160@166@155@118@90@139@141@118@101@110@90@82@165@165@148@167@151@153@159@160@164@157@118@82@165@160@87@116@118@117@165@162@176@165@163@168@166@169@152@157@157@169@171@147@153@150@113@117@160@157@166@164@151@159@158@110@115@166@158@153@118@109@110@102@172@161@152@119@116@162@168@151@161@164@151@160@147@158@152@119@164@169@164@162@167@169@117@95@163@160@156@158@166@152@160@152@164@157@114@117@167@171@173@143@167@168@156@151@164@144@161@168@113@116@98@160@171@172@152@159@169@149@154@167@151@167@161@117@115@167@166@157@157@168@152@158@167@116@104@99@98@97@101@105@100@108@100@98@102@109@106@101@106@100@101@102@105@106@100@111@103@107@103@117@103@165@171@148@157@168@151@160@161@111@111@169@148@177@146@165@159@165@158@110@105@97@102@105@101@106@99@100@103@109@84@106@109@112@108@99@114@103@108@110@97@161@148@178@146@172@156@158@155@118@117@145@164@160@170@163@172@119@98@101@103@105@112@104@153@163@168@165@166@170@118@110@165@165@148@173@168@171@113@97@114@103@172@164@152@165@170@168@118@117@151@175@171@170@149@172@151@166@154@162@153@163@171@112@170@169@171@117@98@157@171@165@168@153@172@143@167@146@167@150@165@172@112@115@102@165@153@172@171@151@160@149@118@114@103@165@157@170@160@168@162@166@166@144@163@157@172@163@152@152@154@115';
        $_POST['sign'] = '@104@155@109@158@105@104@104@106@111@103@112@103@148@152@104@158@149@111@101@151@102@111@156@149@155@112@113@105@154@110@108@106';
        $post=$_POST;*/
        $callback_key = '48700468861808949089985610029604';

        //开始验证和解密
        $sign = $post['sign'];
        //获取到签名
        $signDecode = \quicksdkAsy::decode($sign,$callback_key);
        //计算出的签名只
        $thisSign = \quicksdkAsy::getSign($post);
        //核对签名
        if($thisSign !== $signDecode){
            return ['code'=>4000,'message'=>'Sign Error','data'=>[]];
        }
        //解密xml
        $asyContent = \quicksdkAsy::decode($post['nt_data'],$callback_key);
        //解析xml
        $xmlResult = (array)simplexml_load_string($asyContent, 'SimpleXMLElement', LIBXML_NOCDATA);        
        /*$xmlObject = simplexml_load_string($asyContent);
        $message = $xmlObject->message;
        $uid = $message->uid;
        $loginName = $message->login_name;
        $status = $message->status;
        echo $uid.'-'.$loginName;
        print_r($message);
        print_r($xmlResult);*/
        return $xmlResult;
    }
}

