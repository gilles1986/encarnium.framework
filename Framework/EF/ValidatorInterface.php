<?php

declare(ENCODING = 'utf-8');
namespace Framework\EF;

/**
 * Interface einer Validator-Klasse
 * @author Gilles
 *
 */
interface ValidatorInterface {

	/**
	 * @param unknown_type $content
	 */
	public function validate($content);
}

?>