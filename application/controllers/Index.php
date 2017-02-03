<?php
class IndexController extends \Core\BaseControllers{

    public function init() {
        parent::init();
    }
    
    public function homeAction(){
        $this->forward("Thesis","home");        
    }
    
    public function indexAction(){
    	// echo "hello world";
        $this->forward("Admin","index");        
    }

    public function loginAction(){
        echo "hello world";
        // $this->forward("Adminlogin","login");        
    }
}

