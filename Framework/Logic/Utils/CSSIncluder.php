<?php


namespace Framework\Logic\Utils;

/**
 * class CSSIncluder
 *
 * Bindet den CSSIncluder ein
 *
 * @internal     
 *
 * @package		Framework
 * @subpackage	Controller
 */
class CSSIncluder {
  
  private function __construct(){}
  
  /**
   * Bindet die per jsArray übergebenden JS-Datein ein
   * @param Array $jsArray
   */
  public static function load(Array $cssArray) {
    $cssString = "";
    
    for($i=0; $i < count($cssArray); $i++) {       
        $cssString .= "<link rel='stylesheet' href='includes/css/".$cssArray[$i]."' />\r\n";
    }
    
    
    return $cssString;
      
  }
  
}


?>