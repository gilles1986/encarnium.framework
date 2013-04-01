<?php
/**
 * ActionNotFound
 * 
 * Developed under PHP Version 5.3.1
 *
 * LICENSE: GPL License 3
 *
 * @package      Error
 * @category     Validator
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

namespace Framework\Errors;

/**
 * class ActionNotFound
 * 
 * @desc		ActionNotFound Error
 * @internal    
 *
 */
class ActionNotFoundException extends Exception {

	/**
	 * @return	void
	 */
	public function __construct(){
		parent::__construct("Action wurde nicht gefunden", "1001");
	}
	
}





?>