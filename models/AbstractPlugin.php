<?php

namespace Wame\Core\Models;

use Nette\Object;

abstract class AbstractPlugin extends Object 
{	
	/** @var object */
	protected $composerJson;

	public abstract function getComposerJson();

	public function getName() 
	{
		return $this->getComposerJson()->name;
	}
	
	public function getDependencies() 
	{
		if (property_exists($this->getComposerJson(), 'require')) {
			return (array) $this->getComposerJson()->require;
		} else {
			return [];
		}
	}

	public function getSoftDependencies() 
	{
		if (property_exists($this->getComposerJson(), 'softRequire')) {
			return (array) $this->getComposerJson()->require;
		} else {
			return [];
		}
	}

	public function getAllDependencies() 
	{
		return array_merge($this->getDependencies(), $this->getSoftDependencies());
	}
	
}
