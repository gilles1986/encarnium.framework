<?php

declare(ENCODING = 'utf-8');
namespace Framework\Validators;

class RegEx implements \Framework\EF\ValidatorInterface {

  private $pattern;

  private $negate;

  public function __construct($pattern, $negate = false) {
  	\Framework\Logger::debug('init', __CLASS__);
    
    $this->pattern = $pattern;
    $this->negate = ($negate === "true") ? true : false;
  }


  public function validate($content) {
    \Framework\Logger::debug('init', __CLASS__);
    //\Framework\Logger::debug(print_r($content, true), __CLASS__);
    // Nur prüfen, wenn der pattern nicht leer ist
    if (! empty($this->pattern) && is_string($this->pattern)) {
      try {
      
        if (is_string($content)) {
          // Folgende Zeichen dürfen nur enthalten sein
          if (!$this->negate) {
            if (preg_match($this->pattern, $content, $array)) {
            	// Email gültig, true zurückgeben
            	return true;
            } else {
            	\Framework\Logger::info('Regexp validation failed', __CLASS__);
              // Email nicht gültig, false zurückgeben
              return false;;
            }
          } else {
            // Folgende Zeichen dürfen nicht vorhanden sein
            if (preg_match($this->pattern, $content, $array)) {
              // Email gültig, null zurückgeben
              \Framework\Logger::info('Regexp validation failed', __CLASS__);
              // Email nicht gültig, false zurückgeben
              return false;
              
            } else {
              \Framework\Logger::info('Regexp validation success', __CLASS__);
              return true;
            }
          }
        } else if(is_array($content)) {
        	for($i=0; $i < count($content); $i++) {
        		if($this->negate) {
        			if (preg_match($this->pattern, $content[$i], $array)) {
	              \Framework\Logger::info('Regexp validation failed', __CLASS__);
	              // Entspricht einen nicht erlaubten Pattern
	              return false;
              
              }
        		} else {
        			if(!preg_match($this->pattern, $content[$i], $array)) {
        				\Framework\Logger::info('Negative regexp validation failed', __CLASS__);
        				// Entspricht nicht den erlaubten Pattern
        				return false;
        			}	
        		}
						
        	}
					return true;
        } else {
        	
        }
        
      }
      catch(Exception $e) {
      	\Framework\Logger::warning('preg error: '.preg_last_error().': '.$e, __CLASS__);
      }
    }
    \Framework\Logger::info("pattern was empty or not a string", __CLASS__);
    \Framework\Logger::info(print_r($this->pattern, true), __CLASS__);
    // Wenn pattern leer war, Validierungsergebnis "gültig" (kein Fehler) zurückliefern
    return true;
  }
  
  public function setPattern($pattern) {
    $this->pattern = $pattern;
  }
  
  public function setNegate($negate) {
    $this->negate = $negate;
  }
  
  public function getPattern() {
    return $this->pattern;
  }
  
  public function getNegate() {
    return $this->negate;
  }
  
}
?>