<?php
/**
 * FileNotFound
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
 * class FileNotFound
 * 
 * @desc		FileNotFound Error
 * @internal    
 *
 */
class FileNotFoundException extends Exception {
	
	/**
	 * Constructor
	 * @param String $filename
	 */
	public function __construct($filename){
		parent::__construct("Datei $filename wurde nicht gefunden", "00001");
	}
	
}





?>