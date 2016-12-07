<?php
namespace Addons\Captcha;
class CaptchaUtil {
    private $_img='';

    public function __construct($namespace='',$options=[]){
        require_once "lib/securimage.php";
        $this->_img = new \Securimage($options);
        $this->_img->setNamespace($namespace);
    }
        
    public function show(){
        $this->_img->show();
    }
    
    public function createCode(){
        return $this->_img->createCodeforOutput();
    }
    
    public function outputCaptcha(){
        $this->_img->outputCaptcha();
    }
}

