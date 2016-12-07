<?php
namespace Addons\Sms;
include_once "YTX/SDK/CCPRestSDK.php";
class SmsUtil {
    
    private $_accountSid= '8a48b5515418ae2d01542743e8291c15';
    private $_accountToken= '4899796c83ba4e308f13478752e7316f';
    private $_appId='aaf98f895427cf500154375c935b1751';
    //private $_serverIP='sandboxapp.cloopen.com';
    private $_serverIP='app.cloopen.com';
    private $_serverPort='8883';
    private $_softVersion='2013-12-26';
    private $_registerTid=1;
    //107504,chenyou
    //81542,ty
    
    public  function sendTemplateSMS($to,$datas,$tempId){
        $rest = new \REST($this->_serverIP,$this->_serverPort,$this->_softVersion);
        $rest->setAccount($this->_accountSid,$this->_accountToken);
        $rest->setAppId($this->_appId);
        return $rest->sendTemplateSMS($to,$datas,$tempId);
    }
}
