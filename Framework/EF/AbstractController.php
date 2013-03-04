<?php 
/**
* AbstractController
*
* Developed under PHP Version 5.3.1
*
* LICENSE: GPL License 3
*
* @package      Encarnium Framework
* @category     Cache
* @copyright    Encarnium Group since 2010
* @link         http://encarnium.de/
*
* @since        2008-11-21
* @author       Felix H. <felix@encarnium.de>
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
 * Haupt Action die von anderen Klassen erweitert werden kann
 * 
 * @author Gilles, Nys Standop
 *
 */
abstract class AbstractController {
  
	/**
	 * The Validator
	 * @var \Framework\EF\ValidatorManager $validator
	 */
	private $validator;
	
	/**
	 * Die validierten request-Vars
	 * @var Array
	 */
	private $request;
	
	/**
	 * Der defaultController
	 * @var String
	 */
	private $defaultController = 'Home';
	
	/**
	 * Die defaultAction
	 * @var String
	 */
	private $defaultAction = 'main';
	
  
  /**
   * The Controller-Name
   * @var	string	$controller
   */
  private $controller;
  
  /**
   * The Action-Name
   * @var	string	$action
   */
  private $action;
  
  /**
   * Der Action-Parameter, der im Request mitgegeben wurde
   * @var string $realAction
   */
  private $realAction;
  
  /**
   * SelfReflectedClass
   * @var \ReflectionClass	$reflectionClass
   */
  private $reflectionClass;
  
  /**
   * The extendet Classes
   * @example '\Framework\DAO\Character,\Framework\DAO\User' or array(
   *     '\Framework\DAO\Character',
   *     '\Framework\DAO\User',
   *     '\Framework\DAO\Equipment'
   * );
   * @var string|array	$extClasses
   */
  protected $extClasses;
  
  protected $data = array();
  
  /**
   * The Smarty Class
   * @var \Framework\EF\Smarty	$smarty
   */
  protected $smarty;

  /**
   * @var Application Options
   */
  private $options;

  /**
   * @var Additional Params
   */
  private $params;
  
  /**
   * Constructor
   * @param \Framework\EF\ValidatorManager $validator
   */
  public function __construct(\Framework\EF\ValidatorManager $validator,$options, $action='', $realAction='', $params=false) {
  	//Bindet ExtClasses ein wenn diese vorhanden sind
    $this->__tryLoadExtClasses();

    $this->params = $params;
    $this->options = $options;
    $this->validator = $validator;
    $this->smarty = new \Framework\EF\Smarty();
    $this->request = $this->getRequest();
    $this->smarty->assign("errors", $this->getErrorMessages());
    $this->smarty->assign("request", $this->getRequest());
    $this->smarty->assign("scripts", \Framework\Logic\Utils\JSClassIncluder::load(json_decode(file_get_contents(CONFIG . "javascript.json"))));
    $this->smarty->assign("css", \Framework\Logic\Utils\CSSIncluder::load(json_decode(file_get_contents(CONFIG . "css.json"))));
    //$this->smarty->register_block("t", "smarty_block_translate");
    
    $templatedir = str_replace('\\', '/', VIEW_PATH);
    $this->smarty->setTemplateDir($templatedir);
    
    //Sich selbst als reflectionClass nehmen
    $this->reflectionClass = new \ReflectionClass($this);
  
    $this->realAction = $realAction;



    if($action != '') {
    	$this->action = $action;    	
    }
    else {
    	$this->action = $this->defaultAction;
    }
	
    $this->redirect();
    
    }
    
  /**
   * Funktion läd alle in $extClasses definierten Erweiterungsklassen. Wenn möglich
   * erstellt er die Klasse in $extClasses mit dem key des Klassennamens.
   *
   * @desc Bei Misserfolg wird die Error404 Action aufgerufen.
   * $_REQUEST['extensionError']['actionCalled'] und
   * $_REQUEST['extensionError']['extensionClassFailed']
   * wird gesetzt.
   * @return	void
   */
  private function __tryLoadExtClasses(){
  	//Prüft ob Attribut extClasses befüllt ist
    if (!empty($this->extClasses)){
    	//Wenn es ein String ist
        if (is_string($this->extClasses)){
            $this->extClasses = explode(';', $this->extClasses);
        }
        //Array mit extClasses durchloopen
        foreach ($this->extClasses as $key => $className){
            $classPath = BASE_PATH . str_replace('\\', '/', $className) . '.php';
            if(is_readable($classPath)) {
                include_once $classPath;
                unset($this->extClasses[$key]);
                if (class_exists($className)){
                    $this->extClasses[$className] = new $className();
                }
            } else {
                //extClass nicht vorhanden, error404 wird eingeleitet
                $_REQUEST['extensionError']['actionCalled'] = $_REQUEST['action'];
                $_REQUEST['extensionError']['extensionClassFailed'] = realpath(CLASSES . $className . '.php');
                
                $_REQUEST['action'] = "error404";

                $newRequest = new \Framework\EF\Core($options);
                $newRequest->run();
                die();
            }
        }
    }
  }

  /**
   * Gets the Request
   * @return	array	The Request
   */
  public function getRequest() {
    return $this->validator->getRequest();
  }
  
  
  public function getSession($value=false) {
  	if($value == false) {
  	  return (isset($_SESSION)) ? $_SESSION : null;
  	} else {
  	  return (isset($_SESSION) && isset($_SESSION[$value])) ? $_SESSION[$value] : null;  	  
  	}
  	
  }
  
  public function setSession($key, $value) {
    $_SESSION[$key] = $value;
  }
  
  /**
   * Gets the ErrorMEssages
   * @return	array	The ErrorMEssages
   */
  public function getErrorMessages() {
    return $this->validator->getErrorMessages();
  }
  
  /**
   * Check if Request is valid
   * @return	boolean	IsValid
   */
  public function isValid() {
    return $this->validator->isValid();
  }

  /**
   *
   * @return EF_Smarty
   */
  public function getSmarty() {
    return $this->smarty;
  }
  
  public function doAction($action) {
  	\Framework\Logger::debug("run:: Action ist ".$action, "Core");
		
		$this->actionController = new \Framework\EF\ActionController($action);
		
		 $realAction = '';
		 $actionClassName = null;
     $error = false;
		 try {
		   $actionConfig = $this->actionController->getActionConfig();
		   $realAction = $action;
		   $actionClassName = "\\Controller\\".$actionConfig['controller'];
		 } 
		 catch(\Framework\Errors\FileNotFound $error) {
			 $actionClassName = '\\Controller\\FatalError';
       $error = true;
		 } 
		 catch(\Framework\Errors\ActionNotFoundException $error) {
		   
		 }
		 
      \Framework\Logger::debug("run:: ActionClassName ist ".$actionClassName, "Core");

		 if( $actionClassName && !$error) {

		 		$this->validator = new ValidatorManager($this->actionController->getActionConfig());
			$this->validator->validate($action, $_REQUEST);
			
			$actionClass = new $actionClassName($this->validator, $actionConfig['method'], $realAction);
		 } 
		 else {

		     if(class_exists("\Controller\FatalError")) {
		       new \Controller\FatalError("actionNotFound",htmlspecialchars($action));  
		     } else {
		       echo "<h2>This Action does not exists</h2><p>If you want a custom Error Site please create a FatalError Controller</p>";
		     }
		     		   		   
		 }
  }
  
	/**
	 * Führt wenn vorhanden die Main-Methode aus
	 * und setzt den Controller namen. Und die Smarty-Paths
	 * @author	Nys Standop
	 * @param	string	$action The Action-Name
	 * @return	void
	 */
	protected function redirect($action = '', $isAction=false) {
      if($isAction) {
        $this->doAction($action);
        return true;
      }

	    if(!$this->_prepare($action)) {
	      return false;
	    }

      if($action != '') {
          $this->action = $action;
      }

      //Wenn keine Action übergeben wird soll die defaultAction aufgerufen werden
      ($this->action != '') ? $methode = $this->action : $methode = $this->defaultAction;

      if ($this->reflectionClass->hasMethod($methode)) {
          $this->action = $methode;
          //Ermittelt den Controller namen
          $reflectionMethod = $this->reflectionClass->getMethod($methode);
          $controllerArray = explode('\\',$reflectionMethod->getDeclaringClass()->getName());
          $this->controller = end($controllerArray);
          $this->{$this->action}();
	    }
	    //Leitet sonst zur Main-Action weiter!
	    elseif ($this->reflectionClass->hasMethod($this->defaultAction)) {
	  		$this->action = $this->defaultAction;
	  		//Ermittelt den Controller namen
	    	$reflectionMethod = $this->reflectionClass->getMethod($this->defaultAction);
	    	$controllerArray = explode('\\',$reflectionMethod->getDeclaringClass()->getName());
	    	$this->controller = end($controllerArray);
	    	$this->{$this->action}();	
	    }
	    else {
	    	//@todo Fehlercode anpassen
	    	throw new Exception("Cannot Redirect to method ".  htmlspecialchars($methode), 4000008);
	    }

            $this->_finalise($action);
	}


  /**
   * Weist Template-Variablen Werte zu
   * @param $variable
   * @param $value
   */
  public function assign($variable, $value) {
      $this->smarty->assign($variable, $value);
    }

    /**
     * Rendert die aktuelle Action
     * @author	Nys Standop
     * @return	void
     */
    protected function render($folder = false, $template=false) {
      \Framework\Logger::debug("Controller::render","Controller");
    	//Smarty-Path ändern
    	
        if($template != false) {
          $templatedir = str_replace('\\', '/', VIEW_PATH.$folder.'/');
        } else {
          $templatedir = str_replace('\\', '/', VIEW_PATH.$this->controller.'/');
        }
      
        
        if($template == false) {
          $template = $folder;
        }
      
      
    	$this->smarty->setTemplateDir($templatedir);
    	
    	//Controller aus dem Classname abschneiden
    	$controller = str_replace('Controller', '', $this->controller);
    	/**
    	 * Prüft ob es den compile-Dir schon gibt sonst legt er ihn an
    	 */
    	$cachedir = str_replace('\\', '/', ROOT.'Data/Smarty/compiled/'.$controller.'/');
    	if(!is_dir($cachedir)) {
    		//@todo richtige Fehlerzahl reinschreiben
    		if(!mkdir($cachedir)) { throw new Exception('Can not create Dir', 400005); }
    	}
    	
    	
    	$this->smarty->setCompileDir($cachedir);
    	\Framework\Logger::debug("Controller::render Try to load action-> ".$templatedir.$this->action.".tpl","Controller");
    	if($this->smarty->templateExists($this->action.'.tpl')) {
    	\Framework\Logger::debug("Controller::render -> ".$this->action.".tpl","Controller");
    	//Führt die Action aus
    		$this->smarty->display($this->action.'.tpl');
    	}
    	else {
    	  \Framework\Logger::debug("Controller::render Try to render -> ".$templatedir.$template.".tpl","Controller");
    	  if($this->smarty->templateExists($template.'.tpl')) {
    	    
    	    $this->smarty->display($template.'.tpl');
    	  } else {
    	    //@todo fehlercode anpassen
    		  \Framework\Logger::debug("Controller::render Tried to render -> ".$this->action.".tpl but failed","Controller");
    		  throw new Exception("Template \"".$templatedir.$template.".tpl\" or \"".$templatedir.$this->action.".tpl\"not Found", 47108);
    	  }    		
    	}
    }
    
    protected function addData($key,$data, $secure=false) {
      
      if($key && $data && !$secure) {
        $this->data[$key] = $data;
      } else if($key && $data && $secure){
        if(is_array($data)) {
          $this->data[$key] = $this->secureArrayData($data);
        } else {
          $this->data[$key] = $this->secureData($data);
        }
      }
      
      
    }
    
    protected function secureArrayData($data, $secure=false) {
      $newData = array();
      foreach($data as $key=>$value) {
        $newData[$key] = $this->secureData($value);
      }
      return $newData;
    }
    
    protected function secureData($data) {
      $data = htmlspecialchars($data);
      return $data;
      //return mysql_real_escape_string($data);
    }
    
    protected function getSecureData($key) {
      
    }
    
    /**
     * Default Action, muss überschrieben werden
     * @return void
     */
    public function main() {
       return;
    }

    /**
     * Wird aufgerufen, direkt bevor Action Methode aufgerufen wird.
     * @return void
     */
    public function _prepare($action){
       return true;
    }

    /**
     * Wird aufgerufen, direkt nach Action Methode aufgerufen wurde.
     * @return void
     */
    public function _finalise($action){

    }
  

	public function getAction()
	{
	    return $this->action;
	}

	public function setAction($action)
	{
	    $this->action = $action;
	}

	public function getRealAction()
	{
	    return $this->realAction;
	}

	public function setRealAction($realAction)
	{
	    $this->realAction = $realAction;
	}

  /**
   * @param \Framework\EF\Application $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }

  /**
   * @return \Framework\EF\Application
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * @param \Framework\EF\Additional $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }

  /**
   * @return \Framework\EF\Additional
   */
  public function getParams()
  {
    return $this->params;
  }



}



?>