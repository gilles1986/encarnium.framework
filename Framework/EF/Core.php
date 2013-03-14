<?php
/**
 * Core
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
 * class Core
 *
 * Führt automatisch die per REQUEST übergebene action aus
 *
 * @internal     
 *
 * @package		Framework
 * @subpackage	EF
 */
class Core {
  
	/*
	* Grundliegende EF-Komponenten
	*/
	
	/**
	 * @var \Framework\EF\ActionController
	 */
	private $actionController;
	
	/**
	 * @var \Framework\EF\ValidatorManager
	 */
	private $validator;
	
	/**
	 * @var unknown_type
	 */
	private $rightSystem;

  /**
   * @var Application Options
   */
  private $options;

  private $app_config;
  
	/**
	 * Constructor
	 * @return void
	 */
	public function __construct($app_config, $options) {

		//ClassLoader initializieren
		$this->initializeClassLoader();
    $this->app_config = $app_config;
    $this->options = \Framework\Logic\Utils\Utility::array_merge_recursive_unique($app_config, $options);;
		//DebugExceptionHandler initializieren
		$this->initializeDebugExceptionHandler();			
		//Logger initializieren
		$this->initializeLogger();	 
	}
	
	/**
	 * Setzt alle Constanten die benötigt werden
	 * @author	Nys Standop <nys.standop@yahoo.de>
	 * @return	void
	 */
	public static function setConstants() {
		if(!defined('ROOT')) {
			//Rootverzeichnes ermitteln
			$up = '../';
			define('ROOT', realpath(dirname(__FILE__).'/'.$up.$up).'/');
		}
		if(!defined('CONFIG')) {
			define('CONFIG', ROOT.'Config/');
		}
		if(!defined('CLASSES')) {
			define('CLASSES', ROOT.'Framework/');
		}
	    if(!defined('CRUDS')) {
			define('CRUDS', ROOT.'Packages/CRUD/');
		}
	  if(!defined('CONTROLLER')) {
			define('CONTROLLER', ROOT.'Packages/Controller/');
		}
		if(!defined('PHPLIBS')) {
			define('PHPLIBS', ROOT.'Framework/');
		}
		if(!defined('SMARTY_PLUGINS')) {
			define('SMARTY_PLUGINS', ROOT."Framework/Smarty/plugins/");
		}
		if(!defined('BASE_PATH')) {
			define('BASE_PATH', ROOT.'Packages/');
		}
		
	  if(!defined('SMARTY_PATH')) {
			define('SMARTY_PATH', ROOT.'Framework/Smarty/');
		}
		
		if(!defined('WEB_PATH')) {
			define('WEB_PATH', ROOT.'Web/');
		}
		if(!defined('VIEW_PATH')) {
			define('VIEW_PATH', BASE_PATH.'Views/');
		}
		if(!defined('ACTION_CONFIGS')) {
			define('ACTION_CONFIGS', ROOT.'Config/Actions/');
		}
	}
	
	/**
	 * initialisiert den ClassLoader
	 * @author	Nys Standop	<nys.standop@yahoo.de>
	 * @return	void
	 * @see http://www.php.net/manual/en/function.spl-autoload-register.php
	 */
	protected function initializeClassLoader() {
		$classLoader = realpath(ROOT . 'Framework/Source/ClassLoader.php');
		if(!is_file($classLoader)) throw new \Exception('ClassLoader not Found', 0004);
                require_once $classLoader;
		$this->classLoader = new \Framework\Source\ClassLoader();
		spl_autoload_register(array($this->classLoader, 'loadClass'));
	}
  
	
	/**
	 * initialisiert die Logger-Class
	 * @return void
	 */
	protected function initializeLogger() {
		\Framework\Logger::init();
	}
	
	
	/**
	 * initialisiert den DebugExceptionHandler
	 * @return void
	 */
	protected function initializeDebugExceptionHandler() {
		new \Framework\Errors\DebugExceptionHandler();
	}
	
	/**
	 * Lässt die Application ausführen
	 * @return void
	 */
	public function run($action=false, $validator=false) {
		$actionName = isset($this->options['actionName']) ? $this->options['actionName'] : "action" ;
    if(!$action ) {
      $action = (isset($_REQUEST[$actionName])) ? $_REQUEST[$actionName] : ((isset($this->options['defaultAction'])) ? $this->options['defaultAction'] : 'main') ;
    }

\Framework\Logger::debug("run:: Action ist ".$action, "Core");
		if(class_exists("\\Plugins\\ActionController")) {
      $this->actionController = new \Plugins\ActionController($action, $this->options);
    } else {
      $this->actionController = new \Framework\EF\ActionController($action, $this->options);
    }


    $error = false;
		 $realAction = '';
		 $actionClassName = null;
		 try {
		   $actionConfig = $this->actionController->getActionConfig();
		   $realAction = $action;
		   $actionClassName = "\\Controller\\".$actionConfig['controller'];
		 } 
		 catch( \Framework\Errors\FileNotFound $error) {
       $error = true;

		 } 
		 catch(\Framework\Errors\ActionNotFoundException $error) {
       $error = true;

		 }

\Framework\Logger::debug("run:: ActionClassName ist ".$actionClassName, "Core");
    // Wenn eine Action weitergelietet wurde (z.B. 404)
    if($validator != false) {
       $actionClass = new $actionClassName($this->validator, $this->options, $actionConfig['method'], $realAction, $this->app_config);
       return false;
     }

		 if( $actionClassName && !$error ) {

      if(class_exists("\\Plugins\\ValidatorManager")) {
        $this->validator = new \Plugins\ValidatorManager($this->actionController->getActionConfig());
      } else {
        $this->validator = new \Framework\EF\ValidatorManager($this->actionController->getActionConfig());
      }

			$this->validator->validate($action, $_REQUEST);
			
			$actionClass = new $actionClassName($this->validator, $this->options, $actionConfig['method'], $realAction, $this->app_config);
		 } 
		 else {

       if(class_exists("\\Plugins\\ValidatorManager")) {
         $this->validator = new \Plugins\ValidatorManager(array(
           "name" => "404",
           "displayName" => "Action",
           "args" => array($action)
         ), true);
       } else {
         $this->validator = new \Framework\EF\ValidatorManager(array(
           "name" => "404",
           "displayName" => "Action",
           "args" => array($action)
         ), true);
       }
		     try {
           if(class_exists("\\Plugins\\ActionController")) {
             $actionController =  new \Plugins\ActionController("404", $this->options);
           } else {
             $actionController =  new \Framework\EF\ActionController("404", $this->options);
           }
           $actionController->getActionConfig();
           $this->run("404", true);

		     } catch(\Framework\Errors\ActionNotFoundException $e) {
		       echo "<h2>This Action does not exists</h2><p>If you want a custom Error Site please create an action called 404</p>";
		     }
		     		   		   
		 }

		 
		 
	}
}

?>