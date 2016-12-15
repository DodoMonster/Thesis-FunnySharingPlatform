<?php
class IndexController extends \Core\BaseControllers{

    public function init() {
        parent::init();
    }
    
    public function homeAction(){
        $this->forward("Thesis","home");
        
    }
    
}

