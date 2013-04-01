<?php 


namespace Framework\Validators;

class Number implements \Framework\EF\ValidatorInterface {
  
  
  public function __construct() {
		
  }
  
  public function validate($content) {
	\Framework\Logger::debug("Validator_String aufgerufen", "Validator_String");
  if (intval($content)) {
		return true;
	}
	else return false;
  }
  
  
}


?>