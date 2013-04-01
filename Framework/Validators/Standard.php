<?php


namespace Framework\Validators;

class Standard extends \Framework\Validators\RegEx implements \Framework\EF\ValidatorInterface {
  
  public function __construct() {
    $this->setPattern("/^.*(\"|'|&lt;|&gt;)+.*$/");
    $this->setNegate("true");
    
  }
  
  
}

?>