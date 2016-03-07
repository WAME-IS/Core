<?php

namespace Wame\Core\Models;

use Nette\Utils\Finder;

class ConfigLoader extends \Nette\Object 
{
	/** @var array */
	private $directories = [];
	
	/**
	 * Load *.neon files from directories
	 * 
	 * @param \Nette\Configurator $configurator
	 */
//	public function loadConfigs($configurator)
//	{
//		foreach ($this->directories as $dir) {
//			$this->configs = glob($dir . '/**/**/*.neon');
//		}
//		
//		if (count($this->configs) > 0) {
//			foreach ($this->configs as $config) {
//				$configurator->addConfig($config);
//			}
//		}
//	}
	public function loadConfigs($configurator)
	{
		foreach ($this->directories as $dir) {
			foreach (Finder::findFiles('*.neon')->from($dir) as $path => $configFile) {
				$configurator->addConfig($path);
			}
		}
	}
	
	/**
	 * Add directory
	 * 
	 * @param string $dir
	 * @throws \Exception
	 */
	public function addDirectory($dir)
	{
		if (is_dir($dir)) {
			$this->directories[] = $dir;
		} else {
			throw new \Exception("Directory $dir doesnt exist.");
		}
	}

}
