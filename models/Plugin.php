<?php

namespace Wame\Core\Models;

abstract class Plugin extends AbstractPlugin 
{	
	public function getComposerJson() 
	{
		if (!$this->composerJson) {
			$path = dirname($this->getReflection()->getFileName());
			
			if (file_exists($path . '/' . PluginLoader::COMPOSER_FILE)) {

				$json = file_get_contents($path . '/' . PluginLoader::COMPOSER_FILE);
				$this->composerJson = \Nette\Utils\Json::decode($json);
			} else {
				throw new \Exception('Plugin ' . get_class($this) . ' is missing ' . PluginLoader::COMPOSER_FILE);
			}
		}
		
		return $this->composerJson;
	}

	public function onLoad() {}

	public function onEnable() {}
	
}
