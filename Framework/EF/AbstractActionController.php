<?php
/**
 * EF_ActionController
 *
 * Developed under PHP Version 5.3.1
 *
 * LICENSE: GPL License 3
 *
 * @package      Encarnium Framework
 * @category     ActionController
 * @copyright    Encarnium Group since 2010
 * @link         http://encarnium.de/
 *
 * @since        2008-11-21
 * @author       Gilles Meyer <gilles@encarnium.de>
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
 * class EF_ActionController
 *
 * @desc		Der Action-Controller lädt alle nötigen Klassen für eine Action hinein und gibt den Klassennamen der Startklasse zurück
 * Wenn eine Klasse nicht richtig geladen werden kann, wird auf die Action FatalError weitergeleitet
 * @internal
 *
 */
abstract class AbstractActionController implements \Framework\EF\ActionControllerInterface {



  /**
   * @var String Default Controler
   */
  private $errorController = 'Error404';

  /**
   * @var String Controler
   */
  private $controller;

  /**
   * @var String action
   */
  private $action;

  /**
   * @var Application options
   */
  private $options;




  /**
   * Constructor
   * @param	String $action
   * @return	void
   */
  public function __construct($action, $options) {

    \Framework\Logger::debug("__construct:: Action ist ".$action, "ActionController");
    $this->options = $options;
    // Config parsen
    $this->action = $action;
  }

  /**
   *
   * Liest die Aktionen aus, prüft ob die gewünschte Aktion existiert und lädt die Konfiguration für diese hinein
   * @throws FileNotFoundException
   */
  public function getActionConfig() {
    $fileName = str_replace('\\', '/', ROOT . "Config/actions.json");

    if(realpath($fileName) === false) {
      throw new  \Framework\Errors\FileNotFoundException($fileName);
    }

    $actions = json_decode(file_get_contents($fileName), true);

    //Wenn kein Array vorhanden ist Fehler schmeißen
    if(!is_array($actions)) { throw new Exception("Error while loading actions.json: invalid json?"); }

    if(isset($actions[$this->action]) && $actions[$this->action] != "") {
      // Action reinladen
      $actionConfigName = ($actions[$this->action] === "") ? $this->action : $actions[$this->action];
      $fileName = str_replace('\\', '/', ACTION_CONFIGS . strtolower($actionConfigName) . ".json");

      if(!realpath($fileName)) {
        throw new \Framework\Errors\FileNotFoundException($fileName);
      }

      $actionConfig = json_decode(file_get_contents($fileName), true);
      //Wenn kein Array vorhanden ist Fehler schmeißen
      if(!is_array($actionConfig)) { throw new Exception(); }

      return $actionConfig;


    }
    else {
      //return array('controller' => $this->errorController, 'action' => $this->defaultAction);
      throw new \Framework\Errors\ActionNotFoundException();
    }

  }




  /**
   * Gibt den Controller Classennamen zurrück
   * @return	string	The ControllerClassName
   */
  public function getControllerClassName() {

    $actionConfig = $this->getActionConfig();

    // Wenn keine Action gewählt wurde eine setzen
    $controller = $actionConfig['controller'];

    $className = ucfirst($controller);

    $className = "\\Framework\\Controller\\".$className;

    //Überprüft ob es die Datei gibt, wenn nicht wird ein Fehler produziert
    if(realpath(str_replace('\\', '/', BASE_PATH .$className.'.php')) == false) {
      throw new Exception("ControllerClass not Found", '10004');
    }

    return $className;
  }

}
?>