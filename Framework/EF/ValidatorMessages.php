<?php
/**
 * EF_ValidatorMessages
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
namespace Framework\EF;

/**
 * class EF_ValidationManager
 *
 * @desc		
 * @internal     
 *
 */
use Framework\Logger;

class ValidatorMessages {
	
	/**
	 * Beinhaltet die Fehler Meldungen der INI-Datei
	 * @var Array
	 */
	private $errorMessages = array();
	
	/**
	 * Beinhaltet nur die gesuchte Meldung
	 * @var String
	 */
	private $errorMessage;
	
	/**
	 * Die Array zum ersetzen der <b>%eineZahl%</b> in der Fehler Meldung
	 * @var Array
	 */
	private $errorArrayArg;
	
	/**
	 * Die Anzahl der gefundnen Argumente
	 * @var Integer
	 */
	private $lenght;
	
	/**
	 * Der zum ersetzene Test z.B <b>%0%</b>
	 * @var unknown_type
	 */
	private $replace;
	
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $configMessages;
	
	/**
	 * Constructor
	 * @return	void
	 */
	public function __construct() {
	 $this->configMessages = \Framework\Logic\Utils\jsonHandler::parseJson(ROOT."Config/validator_errorMessages.json");
	}
	
	private function getValidatorName($validator) {
	  $valName = strrev($validator);
	  $pos = strPos( $valName, "\\");
    Logger::debug(strrev(substr($valName, 0, $pos)),"Test1");
	  return strrev(substr($valName, 0, $pos));
	}
	
	/**
	 * Gibt die fertige Fehler Meldung zurück
	 * $validator -> Den Validator
	 * $fieldName -> Den Feld Namen
	 * $args -> Argumente
	 */
	public function addError($validator, $fieldName, $args, $forcename=false) {
		//Holt die Fehlermeldung für den Validator
    if($forcename) {
      $valName = $validator;
    } else {
      $valName = $this->getValidatorName($validator);
    }

	  if(isset($this->configMessages[$valName])){
			$this->errorMessage = $this->configMessages[$valName];
			
		} else {
		  \Framework\Logger::warning("Error Message für ".$valName." existiert nicht", "ValidatorMessages");
			// @TODO Error werfen lassen
		  throw new \Framework\Errors\WrongConfigurationException("Error Message für Validator wurde nicht gefunden","1102");
		}		
		
		$this->errorMessage = str_replace("%displayName%", $fieldName, $this->errorMessage);
		
		//Suchen nach der Arrgumente die in der Meldung verwendet werden und in einer Array setzen nach den Muster:
		/*
		 * Array (
		 * 	(
    			[0] => Array
        		(
            		[0] => %0%
            		[1] => %1%
       			)

    			[1] => Array
        		(
            		[0] => 0
            		[1] => 1
        		)

		 *	)
		 */			
		preg_match_all('/%(\d*?)%/',$this->errorMessage,$this->errorArrayArg);
		
		//Zählen der Arrgumente die im Text vor kommen
		
		//Ersetzen der Argumente
		for($i=0;$i<count($this->errorArrayArg[0]);$i++){
			
			//Text der ersetzt werden muss
			$replace = $this->errorArrayArg[0][$i];
			
			if(!isset($args[$this->errorArrayArg[1][$i]])) {
			  $args[$this->errorArrayArg[1][$i]] = "";
			}
			//Ersetzen des Textes
			$this->errorMessage = str_replace($replace, $args[$this->errorArrayArg[1][$i]], $this->errorMessage);
		}
		
		//Ausgabe
		array_push($this->errorMessages, $this->errorMessage);
		
	}
	
	/**
	 * Gibt die ErrorMessages aus
	 * @return	void
	 */
	public function getErrorMessages() {
	  return $this->errorMessages;
	}
	
}

?>