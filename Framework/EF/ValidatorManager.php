<?php


/**
 * EF_ValidationManager
 * 
 * Developed under PHP Version 5.3.1
 *
 * LICENSE: GPL License 3
 *
 * @package      Encarnium Framework
 * @category     Validator
 * @copyright    Encarnium Group since 2010
 * @link         http://encarnium.de/
 *
 * @since        2008-11-21
 * @author       Gilles Meyer <gilles@encarnium.de>
 *
 *
 *
 * @revision     $Revision: 363 $
 * @modifiedby   $Author: g.meyer $
 *
 */

declare(ENCODING = 'utf-8');
namespace Framework\EF;

/**
 * class EF_ValidationManager
 *
 * @desc	Der Validationsmanager liest für eine Aktion die Formdaten aus und validiert diese
 * Dafür werden die Aktionen samt Formulardaten aus einer JSON-Datei gelesen und danach einzelnd validiert
 * @internal     
 *
 */
use Framework\Logger;

class ValidatorManager {
  
  /**
   * @var Array
   */
  private $request = array();
  
  /**
   * @var String
   */
  private $jsonContent;
  
  /**
   * @var unknown_type
   */
  private $formFields;
  
  /**
   * @var string
   */
  private $action;
  
  /**
   * @var Array
   */
  private $errorMessages = array();
  
  /**
   * @var Boolean
   */
  private $valid = true;



  
  
  /**
   * Konstruktor der Klasse. Liest die Validator Konfiguration ein.
   * @return	void
   */
  public function __construct($actionConfig, $preDefinedError=false) {

    $this->jsonContent = $actionConfig;
    $this->errorMessages = new \Framework\EF\ValidatorMessages();
    if($preDefinedError != false) {
      Logger::debug("predefinedError: ".print_r($actionConfig, true), "ValidatorManager");
      $this->errorMessages->addError($actionConfig['name'], $actionConfig['displayName'], $actionConfig['args'], true);
    }
  }
  
  /**
   * Enter description here ...
   * @param String	$action
   * @param Array	$request
   */
  public function validate($action, $request) {
  	if(count($this->errorMessages->getErrorMessages()) > 0) return false;
    $this->action = $action;
   
    \Framework\Logger::debug("Action \"".$action."\" wird vom Validator-Manager validiert", "ValidatorManager");
    $request2 = $request;
    if(isset($request2['password'])) {
      $request2['password'] = "****";
    }
    \Framework\Logger::debug("Request : \r\n".var_export($request2, true), "ValidatorManager");
    // Formulardaten für Action auslesen
    //$this->readFormAction();  
    $this->validateForm($request);
      
  }
  
  /**
   * Liest alle Formulardaten für die jeweilige Action aus
   * @return	void
   */
  private function readFormAction() {
    $this->formFields = $this->getJsonContent();
  }
  
  /**
   * 
   * Enter description here ...
   * @param		unknown_type $request
   * @return	void
   */
  private function validateForm($request) {
    $count = isset($this->jsonContent['validation']) ? count($this->jsonContent['validation']) : 0;
    for($i=0; $i < $count; $i++) {
      $this->validateField($this->jsonContent['validation'][$i], $request);
    }
  }

  /**
   * Ermittelt JsonContent der zugehörigen Action und gibt diese zurück.
   * @return Array
   */
  private function getJsonContent(){
      return isset($this->jsonContent[$this->action])
            ? $this->jsonContent[$this->action]
            : array();
  }
  
  /**
   * Lässt für ein Feld die benötigten Validatoren durchlaufen
   * @param String $field
   * @param Array $request
   */
  private function validateField($field, $request) {
    if (!isset($field['initValue'])){
        $field['initValue'] = '';
    }

    $validators = $field['validators'];
    $value = (empty($request[$field['name']])) ? $field['initValue'] : $request[$field['name']];
    $displayName = (empty($field['displayName'])) ? $field['name'] : $field['displayName'];
    
    $valid = true;
    // Jeden Validator für das Feld durchgehen
    $count = count($validators);
    for($i=0; $i < $count; $i++) {
    	$validators[$i]['args'] = (isset($validators[$i]['args'])) ? $validators[$i]['args'] : false;
      if(! $this->callValidator($validators[$i]['name'], $displayName, $value, $validators[$i]['args'])) {
        $valid = false;
      }
      
    }
    // Wenn alle Validationen ok waren, dieses Feld freigeben
    if($valid === true) {
      $this->request[$field['name']] = $value;
    }
  }
  
  /**
   * Ruft für ein Feld einen Validator auf und fügt eine ErrorMessage bei nicht valider Eingabe hinzu
   * 
   * @param String $name
   * @param String $displayName
   * @param String $value
   * @param Array $args
   */
  private function callValidator($name, $displayName, $value, $args) {
    try {
      // Es ist möglich über die Reflection Class eine andere Klasse per String zu instanziieren
      $name = ucfirst($name);
      
      $name = '\\Framework\\Validators\\'.$name;
      
      $class = new \ReflectionClass($name);
      $args = (is_array($args)) ? $args : array($args);
      // Klasse instanziieren
      $instance = $class->newInstanceArgs($args);
      
      if(!$instance->validate($value)) {
        $argsAsString = "";
        $count = count($args);
        for($i=0; $i < $count; $i++) { $argsAsString .= "<br/> Argument $i: ".$args[$i]."<br/>";}
         $this->errorMessages->addError($name, $displayName, $args);
        $this->valid = false;
        return false;
      } 
      else {
        return true;
      }
      
      
    } catch(Exception $error) {
      echo "Fehler: ".$error->getMessage()."<br/>";
    }
  }
  
  
 /**
   * Prüft ob die angegebene Aktion vorhanden ist
   * @param String $action Name der Aktion
   */
  private function hasAction() {
  	
    if(empty($this->jsonContent[$this->action])) {
      new \Framework\Errors\ActionNotFoundException();
    } 
    else {
      return true;
    }
    
  }
  
  /**
   * Gibt TRUE wenn Valid, andernfalls FALSE zurück
   * @return Boolean
   */
  public function isValid() { return $this->valid; }
  
  /**
   * Gibt die ErrorMessages zurrück
   * @return	String
   */
  public function getErrorMessages() {
    return $this->errorMessages->getErrorMessages();
  }
  
  /**
   * Gibt den Request zurrück
   * @return unknown_type
   */
  public function getRequest() {
    return $this->request;
  }
  
  
}


?>