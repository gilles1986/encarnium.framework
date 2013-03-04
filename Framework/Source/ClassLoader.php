<?php

declare(ENCODING = 'utf-8');
namespace Framework\Source;


class ClassLoader {
	
	
	public $classes = array();
	
	
	public function __construct() {
	}
		
	/**
	 * 
	 * Klasse wird nur eingebunden wenn 
	 * sie bisland nicht eingebunden wurde
	 * 
	 * @param 	String	$class
	 * @return	void
	 * @todo	Benötigt noch eine Prüfung ob \ am Anfang steht
	 */
	public function loadClass($class) {
      /**
		 * Für Smarty
		 */
		if($class === 'Smarty' && !in_array($class, $this->classes)) {
			$file = realpath( SMARTY_PATH.'Smarty.class.php');	
		    $this->classes[$class] = array('file' => $file);
			if(!file_exists($file)) {
				throw new Exception('File '.$file.' not found');
			}
			require_once $file;
			return;
		}
	    
	    
		  

		//include_once SMARTY_PLUGINS."block.translate.php";
		
		/**
		 * Für Smarty
		 */
		$_class = strtolower($class);
   		/*if ((substr($_class, 0, 16) === 'smarty_internal_' || $_class == 'smarty_security') && !in_array($class, $this->classes)) {
        	$file = SMARTY_PLUGINS . $_class . '.php';
        	
        	$this->classes[$class] = array('file' => $file);
   			if(!file_exists($file)) {
				throw new Exception('File '.$file.' not found');
			}
        	require_once $file;
        	return;
   		}*/		

		if(!in_array($class, $this->classes)) {
			
			if(!is_string($class) || !trim($class)) {
				throw new Exception('No confirmed Classname');
			}
			
			//Erstes zeichen abschneiden			
			(substr($class,0,1) == '\\' ) ? $class = substr($class, 1) : '';

			$file = $class. '.php';
			$file1 =  str_replace('\\', '/', ROOT .$file);
            $file2 =  str_replace('\\', '/', BASE_PATH .$file);

            $this->classes[$class] = array('file' => $file);
	
			
			if(!file_exists($file1)) {
			   if(!file_exists($file2)) {
                   // throw new Exception('File '.$file.' not found');
                   return;
               } else {
                   $this->classes[$class] = array('file' => $file2);
                   require_once $file2;
               }

			} else {
             $this->classes[$class] = array('file' => $file1);
              require_once $file1;
			return;
			}

			
		}
			
	}
	
	
}

?>