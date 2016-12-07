<?php
namespace Views\Smarty;
class Adapter implements \Yaf_View_Interface{
    public $_smarty;
    public function __construct() {
        require "Smarty.class.php";
        $this->_smarty = new \Smarty();
        $this->_smarty->caching = false;
        $this->_smarty->cache_lifetime=30;//60*60*24*7;
        $this->_smarty->template_dir = SITE_PATH."/application/views/";
        $this->_smarty->compile_dir = SITE_PATH."/data/_runtime/templates_c/";
        $this->_smarty->cache_dir = SITE_PATH."/data/_runtime/cache/";
        $this->_smarty->left_delimiter = "<{";
        $this->_smarty->right_delimiter = "}>";
        //定义模板路径
        $this->assign('TPL_PATH', '../../public/default');//相对路径的问题
    }
    
    public function assign($spec, $value = null) {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }
        $this->_smarty->assign($spec, $value);
    }
    
    public function render($name, $value = NULL) {
        return $this->_smarty->fetch($name);
    }

    public function display($name, $value = NULL) {
        echo $this->_smarty->fetch($name);
    }
    
    public function getEngine() {
        return $this->_smarty;
    }
    
    public function setScriptPath($path){
        if (is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return;
        }
        throw new Exception('Invalid path provided');
    }
    
    public function getScriptPath(){
        return $this->_smarty->template_dir;
    }
    
    public function __set($key, $val){
        $this->_smarty->assign($key, $val);
    }
 
    public function __isset($key){
        return (null !== $this->_smarty->get_template_vars($key));
    }
 
    public function __unset($key){
        $this->_smarty->clear_assign($key);
    }
    
    public function clearVars() {
        $this->_smarty->clear_all_assign();
    }
    
}

