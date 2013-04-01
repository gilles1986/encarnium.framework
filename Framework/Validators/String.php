<?php 


namespace Framework\Validators;

class String implements \Framework\EF\ValidatorInterface {
  
  
  public function __construct() {
		
  }
  
  public function validate($content) {
	\Framework\Logger::debug("Validator_String aufgerufen", "Validator_String");
  if ($content != "" && is_string($content)) {
		return true;
	}
	else return false;
  }
  
  
}


?>