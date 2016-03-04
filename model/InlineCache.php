<?php

namespace App\Core\Model;

trait InlineCache 
{
	/** @var array */
	private $inlineCache = [];

	private function inlineCache($name, $callback) 
	{
		if (array_key_exists($name, $this->inlineCache)) {
			return $this->inlineCache[$name];
		} else {
			$result = $callback();
			$this->inlineCache[$name] = $result;
			return $result;
		}
	}

	private function clearInlineCache($name = null) 
	{
		if ($name) {
			unset($this->inlineCache[$name]);
		} else {
			$this->inlineCache = [];
		}
	}

}
