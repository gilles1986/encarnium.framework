<?php 


namespace Controller;


class UserSite extends \Controller\TplAbstractController {

    public function _prepare($action) {
      //echo "prepare User Site";
      $session = $this->getSession("loggedIn");
      \Framework\Logger::debug("Controller UserSite::_prepare: $session","UserSiteController");
     if(!$session) {
        $this->doAction("main");
        return false;
      } else {
        // Template Typen für den eingeloggten Bereich
        $this->setTemplateType("loggedIn");
        return true;
      }
    }
    
    public function loggedIn() {
      $this->view("userSite","loggedIn");
      
    }
}


?>