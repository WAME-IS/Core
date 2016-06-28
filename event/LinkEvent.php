<?php

namespace Wame\Core;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 * 
 * @method String getDestination()
 * @method void setDestination(String $destination)
 * @method array getArgs()
 * @method void setArgs(array $args)
 */
class LinkEvent extends \Nette\Object {
	
	public $destination;
	public $args;
	
	public function __construct($destination, $args = array()) {
		$this->destination = $destination;
		$this->args = $args;
	}
	
}
