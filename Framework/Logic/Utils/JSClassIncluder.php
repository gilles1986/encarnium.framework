<?php
/**
 * JSClassIncluder
 *
 * Developed under PHP Version 5.3.1
 *
 * LICENSE: GPL License 3
 *
 * @package      
 * @subpackage   
 * @category     
 * @copyright    Encarnium Group since 2010
 * @license		 GPL License 3
 * @link         http://encarnium.de/
 *
 * @since        2008-11-21
 * @author       Username <yourEmail@adress.de>
 *
 *
 *
 * @revision     $Revision: 353 $
 * @modifiedby   $Author: g.meyer $
 *
 */
declare(ENCODING = 'utf-8');
namespace Framework\Logic\Utils;

/**
 * class JSClassIncluder
 *
 * Bindet den JSClassIncluder ein
 *
 * @internal     
 *
 * @package		Framework
 * @subpackage	Controller
 */
class JSClassIncluder {
  
  private function __construct(){}
  
  /**
   * Bindet die per jsArray übergebenden JS-Datein ein
   * @param Array $jsArray
   */
  public static function load(Array $jsArray) {
    $scriptString = "";
    
    for($i=0; $i < count($jsArray); $i++) {       
        $scriptString .= "<script type='text/javascript' src='includes/js/".$jsArray[$i]."'></script>\r\n";
    }
    
    
    return $scriptString;
      
  }
  
}


?>