<?php 


namespace Framework\Validators;

class Matching implements \Framework\EF\ValidatorInterface {
  
  private $match;
  
  public function __construct($match) {
    
    if(empty($match)) throw new \Framework\Errors\NoMatchSpecified();
    
  }
  
  public function validate($content) {
  	return false;
  }
  
  
}


?>