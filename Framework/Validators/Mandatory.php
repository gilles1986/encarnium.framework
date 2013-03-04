<?php

declare(ENCODING = 'utf-8');
namespace Framework\Validators;

class Mandatory implements \Framework\EF\ValidatorInterface {
  
	public function __construct(){}
	
	public function validate($content) {
	    if(strlen($content) > 0) {
	      return true;
	    } else {
	      return false;
	    }
  	}
}


?>