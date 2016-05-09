<?php

namespace Wame\Core\Registers;

use Nette\InvalidArgumentException,
	Nette\Utils\ArrayList;

/**
 * BaseRegister
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class BaseRegister extends ArrayList {

	/** @var string Type */
	private $type;

	public function __construct($type) {
		$this->type = $type;
	}

	/**
	 * Register entity to register.
	 * 
	 * @param mixed $entry
	 */
	public function register($entry) {

		if (!$entry) {
			throw new InvalidArgumentException("Trying to register " . $entry . " into register of " . $this->type);
		}

		$entries = func_get_args();
		foreach ($entries as $entry) {
			$implements = class_parents($entry);
			$implements[] = get_class($entry);
			if (in_array($this->type, $implements)) {
				$this[] = $entry;
			} else {
				throw new InvalidArgumentException("Trying to register class " . get_class($entry) . " into register of " . $this->type);
			}
		}
	}

}
