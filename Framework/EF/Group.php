<?php
/**
 * EF_Group
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
 * class EF_Group
 *
 * ADD DESCRIPTION HERE
 *
 * @internal     
 *
 */
class Group {
	
	/**
	 * @var Object GroupDAO
	 */
	private $groupDAO;
	
	/**
	 * Constructor
	 * @return void
	 */
	public function __construct() {
		$this->groupDAO = new GroupDAO();
	}
	
}

?>