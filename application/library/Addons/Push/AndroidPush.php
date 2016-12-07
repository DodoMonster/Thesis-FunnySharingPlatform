<?php
namespace Addons\Push;
require 'lib/Channel.class.php';
class AndroidPush {
    public $_apiKey = "C6foBEe4bMs9cmRWSTwxPYGS";
    public $_secretKey = "GIW50DuThoK0fbCU3TYlVHVCuVxZsxAx";
    
    public function pushMessage($from_user, $device_token, $type, $content = ''){
        if (!empty($device_token) && strlen($device_token) == 18) {
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
            $this->sentMessageAndroid($device_token, '享应', $message,$badge);
        }
    }

    //推送android设备消息
    public function sentMessageAndroid($userId,$title,$description,$badge) {
        $channel = new \Channel($this->_apiKey, $this->_secretKey, array(CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 5));
        $push_type = 1; //推送单播消息
        $optional[\Channel::USER_ID] = $userId; //如果推送单播消息，需要指定user
        $optional[\Channel::DEVICE_TYPE] = 3;
        $optional[\Channel::MESSAGE_TYPE] = 0;
        //通知类型的内容必须按指定内容发送，示例如下：
        //$message = json_encode(array('title'=>$title,'description'=>$description,'notification_basic_style'=>7,'open_type'=>1,'url'=>'http://www.isharein.com'));
        $message = json_encode(array('title'=>$title,'description'=>$description,'custom_content'=>array('badge'=>$badge)));
        $message_key = "msg_key";
        $ret = $channel->pushMessage($push_type, $message, $message_key, $optional);
        /*if (false === $ret) {
            $this->error_output('WRONG, ' . __FUNCTION__ . ' ERROR!!!!!');
            $this->error_output('ERROR NUMBER: ' . $channel->errno());
            $this->error_output('ERROR MESSAGE: ' . $channel->errmsg());
            $this->error_output('REQUEST ID: ' . $channel->getRequestId());
        } else {
            $this->right_output('SUCC, ' . __FUNCTION__ . ' OK!!!!!');
            $this->right_output('result: ' . print_r($ret, true));
        }*/
    }

    
    public function test() {
        //$appid='3460300'; 
        //$userId='723663234070838023'; 
        //$channelId='4232772410267011700'; 

        $appid = '3460300';
        $userId = '816093805121846882';
        $channelId = '3977081384428362052';
        $title='享应';
        $description='好应用';
        
        $allowPlatform=\Yaf_Registry::get("sysconfig")->sys->allow->platform;
        //$this->sentMessageAndroid($userId,$title,$description);
        //$this->test_fetchMessageCount($userId);
    }
    
    function test_fetchMessageCount($apiKey,$secretKey,$userId) {
        $channel = new \Channel($apiKey, $secretKey);
        $ret = $channel->fetchMessageCount($userId);
        if (false === $ret) {
            $this->error_output('WRONG, ' . __FUNCTION__ . ' ERROR!!!!!');
            $this->error_output('ERROR NUMBER: ' . $channel->errno());
            $this->error_output('ERROR MESSAGE: ' . $channel->errmsg());
            $this->error_output('REQUEST ID: ' . $channel->getRequestId());
        } else {
            $this->right_output('SUCC, ' . __FUNCTION__ . ' OK!!!!!');
            $this->right_output('result: ' . print_r($ret, true));
        }
    }

    function error_output($str) {
        echo "\033[1;40;31m" . $str . "\033[0m" . "\n";
    }

    function right_output($str) {
        echo "\033[1;40;32m" . $str . "\033[0m" . "\n";
    }

}
