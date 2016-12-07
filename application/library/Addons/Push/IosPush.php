<?php
namespace Addons\Push;
class IosPush {
    private $_passphrase = 'a320goople';
    public function pushMessage($from_user, $device_token, $type, $content = '') {
        if (!empty($device_token) && strlen($device_token) == 64) {
            switch (strtolower($type)) {
                case 'comment':
                    $message = "你的朋友刚刚评论了你的动态，快点击查看吧。";
                    $badge='2';
                    break;
                case 'replycomment':
                    $message = "你的朋友刚刚回复了你的评论，快点击查看吧。";
                    $badge='2';
                    break;
                case 'follow':
                    $message = "有人刚刚关注你了，快点击查看吧。";
                    $badge='5';
                    break;
                case 'atme':
                    $message = "你朋友刚刚发表了提到你的动态信息，快点击查看吧。";
                    $badge='1';
                    break;
                case 'praise':
                    $message = "你朋友赞了你的动态信息，快点击查看吧。";
                    $badge='3';
                    break;
                case 'message':
                    $message = $content;
                    $badge='4';
                    break;
            }
            $environment = \Yaf_Registry::get("sysconfig")->sys->environment;
            switch ($environment){
                case 0:
                    $this->sentSandBoxPush($device_token, $message,$badge);
                    break;
                case 1:
                    $this->sentProductionPush($device_token, $message,$badge);
                    break;
            }
        }
    }

    //TODO查一查苹果官方推送消息的文档
    public function sentSandBoxPush($deviceToken, $message,$badge) {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', APPLICATION_PATH . '/library/Addons/Push/Pem/server_certificates_bundle_sandbox.pem');
        //stream_context_set_option($ctx, 'ssl', 'passphrase', SITE_PATH.'/library/Addons/Push/Pem/entrust_root_certification_authority.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);
        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 15, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp){
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        }
       // $body['aps'] = array('alert' => $message, 'sound' => 'default','badge'=>$badge);
        //$payload = json_encode($body);
        //$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        $payload = json_encode(array('aps'=>array('alert' => $message, 'sound' => 'default','badge'=>$badge)));
        $id=time();
        $expire = time() + 3600;
        $msg=pack('CNNnH*n',1,$id,$expire,32,$deviceToken,strlen($payload)).$payload;//Enhanced mode
        //$msg=pack('CnH*n',0,32,$deviceToken,  strlen($payload)).$payload;//Simple mode
        $result = fwrite($fp, $msg, strlen($msg));
        if (!$result){
            echo 'Message not delivered' . PHP_EOL;
        }else{
            //echo 'Message successfully delivered' . PHP_EOL;
        }
        fclose($fp);
    }

    public function sentProductionPush($deviceToken, $message,$badge) {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', APPLICATION_PATH . '/library/Addons/Push/Pem/server_certificates_bundle_production.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 15, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp){
            //exit("Failed to connect: $err $errstr" . PHP_EOL);
            return;
        }
        $payload = json_encode(array('aps'=>array('alert' => $message, 'sound' => 'default','badge'=>$badge)));
        $id=time();
        $expire = time() + 3600;
        $msg=pack('CNNnH*n',1,$id,$expire,32,$deviceToken,strlen($payload)).$payload;//Enhanced mode
        //$msg=pack('CnH*n',0,32,$deviceToken,  strlen($payload)).$payload;//Simple mode
        fwrite($fp, $msg, strlen($msg));
        fclose($fp);
    }
    
    public function pushManyMessage($from_user,$deviceTokenArray,$type,$message='') {
        $environment = \Yaf_Registry::get("sysconfig")->sys->environment;
        $badge='4';
        switch ($environment){
            case 0:
                $this->sentManySandBoxPush($deviceTokenArray, $message,$badge);
                break;
            case 1:
                $this->sentManyProductionPush($deviceTokenArray, $message,$badge);
                break;
        }
    }

    public function sentManySandBoxPush($deviceTokenArray, $message, $badge,$errno=0, $errstr='') {
        $cnt=30;
        $times=ceil(count($deviceTokenArray)/$cnt);
        for($i=0;$i<$times;$i++){
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', APPLICATION_PATH . '/library/Addons/Push/Pem/server_certificates_bundle_sandbox.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);
            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $errno, $errstr, 30, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            if ($fp){
                $payload = json_encode(array('aps'=>array('alert' => $message, 'sound' => 'default','badge'=>$badge)));
                $id=time();
                $expire = time() + 3600;
                //foreach ($deviceTokenArray as $deviceToken){
                for($j=$i*$cnt;$j<($i*$cnt+$cnt);$j++){
                    if($i==$times-1 && !isset($deviceTokenArray[$j])){
                        break;
                    }
                    $deviceToken=$deviceTokenArray[$j];
                    $msg=pack('CNNnH*n',1,$id,$expire,32,$deviceToken,strlen($payload)).$payload;//Enhanced mode
                    //$msg=pack('CnH*n',0,32,$deviceToken,  strlen($payload)).$payload;//Simple mode
                    fwrite($fp, $msg, strlen($msg));
                }
            }
            fclose($fp);
            usleep(1000);
        }
    }
    
    public function sentManyProductionPush($deviceTokenArray, $message,$badge, $errno=0, $errstr='') {
        $cnt=30;
        $times=ceil(count($deviceTokenArray)/$cnt);
        for($i=0;$i<$times;$i++){
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', APPLICATION_PATH . '/library/Addons/Push/Pem/server_certificates_bundle_production.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);
            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $errno, $errstr, 30, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            if ($fp){
                $payload = json_encode(array('aps'=>array('alert' => $message, 'sound' => 'default','badge'=>$badge)));
                $id=time();
                $expire = time() + 3600;
                for($j=$i*$cnt;$j<($i*$cnt+$cnt);$j++){
                    if($i==$times-1 && !isset($deviceTokenArray[$j])){
                        break;
                    }
                    $deviceToken=$deviceTokenArray[$j];
                    $msg=pack('CNNnH*n',1,$id,$expire,32,$deviceToken,strlen($payload)).$payload;//Enhanced mode
                    fwrite($fp, $msg, strlen($msg));//判断失败重新开启另一个句柄    
                }
            }
            fclose($fp);
            usleep(1000);
        }
    }
    
    public function getSandBoxFeedback($errno=0, $errstr=''){
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', APPLICATION_PATH . '/library/Addons/Push/Pem/server_certificates_bundle_sandbox.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);
        $fp = stream_socket_client('ssl://feedback.sandbox.push.apple.com:2196', $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp){
            echo "Failed to connect: $errno $errstr" . PHP_EOL;
            return FALSE;
        }
        $tuples = stream_get_contents($fp);
        fclose($fp);
        if ($tuples === false){
            echo 'Failed to download device tokens' . PHP_EOL;
            return FALSE;
        }
        return $tuples;
    }
    
    public function getProductionFeedback($errno=0, $errstr=''){
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', APPLICATION_PATH . '/library/Addons/Push/Pem/server_certificates_bundle_production.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);
        $fp = stream_socket_client('ssl://feedback.push.apple.com:2196', $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp){
            echo "Failed to connect: $errno $errstr" . PHP_EOL;
            return FALSE;
        }
        $tuples = stream_get_contents($fp);
        fclose($fp);
        if ($tuples === false){
            echo 'Failed to download device tokens' . PHP_EOL;
            return FALSE;
        }
        return $tuples;
    }
}
