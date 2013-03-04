<?php
/**
 * WrongConfiguration
 * 
 * Developed under PHP Version 5.3.1
 *
 * LICENSE: GPL License 3
 *
 * @package      Error
 * @category     Core
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
namespace Framework\Errors;

/**
 * class WrongConfiguration
 * 
 * @desc		WrongConfiguration Error
 * @internal    
 *
 */
class WrongConfigurationException extends Exception {
	
	/**
	 * Constructor
	 * @return	void
	 */
	public function __construct(){
		parent::__construct("Falsch konfigurierter Parameter", "0002");
	}
	
}





?>