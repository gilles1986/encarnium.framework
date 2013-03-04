<?php 

declare(ENCODING = 'utf-8');
namespace Framework\Validators;

class Length implements \Framework\EF\ValidatorInterface {
  
  private $min;
  private $max;
  
  public function __construct($min=0, $max=false) {
    
    if(is_int($min) && is_int($max)) {
      if($min > $max) {
        throw new \Framework\Errors\WrongConfigurationException();
      }      
    } 
    
    $this->min = ($min >= 0) ? $min : 0;
    $this->max = ($max != false && $max > 0) ? $max : false;
    
  }
  
  public function validate($content) {
  if ($content != "" && is_string($content)) {
      if(strlen(trim($content)) >= $this->min) {
        if($this->max != false ) {
          if(strlen(trim($content)) > $this->max) {
            return false;
          }
        } 
      } else {
        return false;
      }
      return true;
    } else {
      return false;
    }
  }
  
  
}


?>