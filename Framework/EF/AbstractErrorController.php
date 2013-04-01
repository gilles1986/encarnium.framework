<?php 
/**
* AbstractErrorController
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
* @author       Gilles M. <gilles@encarnium.de>
*
*
*
* @revision     $Revision: 353 $
* @modifiedby   $Author: g.meyer $
*
*/


namespace Framework\EF;

/**
 * Haupt Action die von anderen Klassen erweitert werden kann
 * 
 * @author Gilles, Nys Standop
 *
 */
abstract class AbstractErrorController {
  
		
  /**
   * Die validierten request-Vars
   * @var Array
   */
  private $request;
  
  private $errorCode;
  
  private $defaultAction = "main"; 
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
  
  /**
   * The Smarty Class
   * @var \Framework\EF\Smarty	$smarty
   */
  protected $smarty;
  
  /**
   * Constructor
   * @param \Framework\EF\ValidatorManager $validator
   */
  public function __construct($action='', $realAction='', $errorCode='') {
  	//Bindet ExtClasses ein wenn diese vorhanden sind
    $this->__tryLoadExtClasses();

    $this->errorCode = $errorCode;
    
    $this->smarty = new \Framework\EF\Smarty();
    
   
    $this->smarty->assign("scripts", \Framework\Logic\Utils\JSClassIncluder::load(json_decode(file_get_contents(CONFIG . "javascript.json"))));
    $this->smarty->register_block("t", "smarty_block_translate");
    
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
                $newRequest = new \Framework\EF\Core();
                $newRequest->run();
                die();
            }
        }
    }
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
  
  
	/**
	 * Führt wenn vorhanden die Main-Methode aus
	 * und setzt den Controller namen. Und die Smarty-Paths
	 * @author	Nys Standop
	 * @param	string	$action The Action-Name
	 * @return	void
	 */
	protected function redirect($action = '') {
      $this->_prepare($action);

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
     * Rendert die aktuelle Action
     * @author	Nys Standop
     * @return	void
     */
    protected function render($folder=false, $template=false) {
      \Framework\Logger::debug("Controller::render","Controller");

        if($template != false) {
          $templatedir = str_replace('\\', '/', VIEW_PATH.$folder.'/');
        } else {
          $templatedir = str_replace('\\', '/', VIEW_PATH.$this->controller.'/');
        }
      
        if($template == false) {
          $template = $folder;
        }
    	//Smarty-Path ändern
    	
    	
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
    	\Framework\Logger::debug("Controller::render Try to load action-> ".$this->action.".tpl","Controller");
    	if($this->smarty->template_exists($this->action.'.tpl')) {
    	\Framework\Logger::debug("Controller::render -> ".$this->action.".tpl","Controller");
    	//Führt die Action aus
    		$this->smarty->display($this->action.'.tpl');
    	}
    	else {
    	  \Framework\Logger::debug("Controller::render Try to render -> ".$template.".tpl","Controller");
    	  if($this->smarty->template_exists($template.'.tpl')) {
    	    $this->smarty->display($template.'.tpl');
    	  } else {
    	    //@todo fehlercode anpassen
    		  \Framework\Logger::debug("Controller::render Tried to render -> ".$this->action.".tpl but failed","Controller");
    		  throw new Exception("Template not Found", 47108);
    	  }    		
    	}
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
}



?>