<?php
/**
 * UndefinedParameter
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
 * @author       Mark Giraud <infinity@encarnium.de>
 *
 *
 *
 * @revision     $Revision: 353 $
 * @modifiedby   $Author: g.meyer $
 *
 */

namespace Framework\Errors;

/**
 * class UndefinedParameter
 * 
 * @desc		WrongConfiguration Error
 * @internal    
 *
 */
class UndefinedParameterException extends Exception {
	
	/**
	 * 
	 * Constructor
	 * @param String $message
	 */
	public function __construct($message){
		parent::__construct("<strong>Warning:</strong> Undefined Parameter see Log for more Information", "0003");
		$this->msg = $message;
	}
 
	/**
	 * 
	 * @return	void
	 */
	public function log() {
		$lines = explode("#",$this->getTraceAsString());
		\Framework\Logger::warning("Undefined Parameter '".$this->msg."' \r\n in ".$this->getFile()." on Line: ".$this->getLine()."\r\n See Stacktrace for mor information:", "Core");
		\Framework\Logger::warning("STACKTRACE: ".implode("\r\n #",$lines), "Core");
	}
	
}





?>