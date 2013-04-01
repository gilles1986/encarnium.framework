<?php


namespace Controller;

/**
 * Da in der Regel ein Standard-Template benutzt wird, erleichtert diese Klasse den Aufruf
 * @author gilles meyer
 *
 */
class TplAbstractController extends \Framework\EF\AbstractController {

  private $mainTpl = array("_main","main");
  
  public function view($controller=false, $view=false) {
    if($controller==false && $view == false) {
      $this->render();
    } else if($controller == false && $view != false) {
      $this->render($controller);
    } else {
      $this->smarty->assign("tpl", array($controller, $view));
      $this->render($this->mainTpl[0],$this->mainTpl[1]);
    }
    
  }
  
  public function setTemplateType($type) {
    if($this->smarty->templateExists("tplTypes/".$type.".tpl")) {
      $this->smarty->assign("tplType", $type); 
    } 
  }


  
}

?>