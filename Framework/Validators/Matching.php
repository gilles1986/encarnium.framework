<?php 

declare(ENCODING = 'utf-8');
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