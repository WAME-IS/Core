<?php

namespace Wame\Core\Models;

use Nette\Utils\Finder;

class TempPlugin extends AbstractPlugin 
{
	/** @var string */
	private $composerPath;
	
	/** @var array */
	private $configs = [];

	public function __construct($composerPath) 
	{
		$this->composerPath = $composerPath;

		foreach (Finder::findFiles('*.neon')->from(dirname($composerPath)) as $path => $configFile) {
			$this->configs[] = $path;
		}
	}

	public function getComposerJson() 
	{
		if (!$this->composerJson) {
			$json = file_get_contents($this->composerPath);
			$this->composerJson = \Nette\Utils\Json::decode($json);
		}
		
		return $this->composerJson;
	}

	public function getConfigs() 
	{
		return $this->configs;
	}

}
