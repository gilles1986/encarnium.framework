<?php 

declare(ENCODING = 'utf-8');
namespace Controller;


class Login extends \Controller\TplAbstractController {

   public function _prepare($action) {
     // Easy way to include your JS Scripts
     \Framework\Logger::debug(__CLASS__."::_prepare","login"); // Logging class

     $scripts = \Framework\Logic\Utils\JSClassIncluder::load(json_decode(file_get_contents(CONFIG . "javascript.json")));
     $this->smarty->assign("jsscript", $scripts);

     $this->smarty->assign("test", "Testvalue in every Action");
     return true;
   }


    public function main() {


      if($this->getSession("loggedIn")) {
      	// Eingeloggt? Dann zur Startseite weiterleiten
      	$this->doAction("loggedIn");
      } else {
        // Ansonsten render Login
        $this->setTemplateType("loggedIn");
        $this->smarty->assign("data", $this->data);
      	$this->view("Login","login");
      }      
      
    }
    
    public function loginAction() {
      // Eingaben OK ?
      if(!$this->isValid()) {
        $this->addData("request", $_REQUEST, true);
        $this->redirect("main");
      } else {
       // Login prüfen
       $request = $this->getRequest();

       $login = new \Models\Login($request);
       if($login->checkLogin()) {
         // Login war erfolgreich
         $this->setSession("user", $request["username"]);
         $this->setSession("loggedIn", true);
         $this->redirect("loggedIn", true);
       } else {
         $this->redirect("main");
       }
      }
    }
    
    public function logoutAction() {
      session_destroy();
      $this->view("Login","login");
    }
}


?>