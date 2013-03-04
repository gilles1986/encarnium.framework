<?php

declare(ENCODING = 'utf-8');
namespace Framework\EF;

/**
 * 
 * Enter description here ...
 * @author Nys Standop
 *
 */
class View {
	
	
	private $smarty;
	
	
	public function __destruct() {
		$this->smarty = new \Framework\EF\Smarty();
	}
	
	/**
	 * Rendert die Ausgabe
	 * @return void
	 */
	public function render() {
		$this->smarty>display('charactereditor.tpl');
	}
	
	/**
	 * Assigns values to template variables
     * @param array|string $var the template variable name(s)
     * @param mixed $value the value to assign
	 */
	public function assign($var, $value = null) {
		$this->smarty->assign($var, $value);
	}
	
}