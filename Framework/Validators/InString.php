<?php 


namespace Framework\Validators;

class InString implements \Framework\EF\ValidatorInterface {
  
  private $string;
  
  public function __construct($inString) {
    $this->string = $inString;    
  }
  
  
  public function validate($content) {
    if(strpos($content,$this->string) !== false) {
      return true;
    } else {
      return false;
    }
  }
  
  
  
}


?>