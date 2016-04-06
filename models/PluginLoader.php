<?php

namespace Wame\Core\Models;

use Tracy\Debugger;

class PluginLoader extends \Nette\Object 
{
	const COMPOSER_FILE = 'composer.json';

	private $noPlugins;

	/** @var array */
	private $pluginDirectories = [];

	/** @var array */
	private $tempPlugins;

	/** @var array */
	private $plugins = [];

	/** @var float */
	private $pluginLoadTime = 0;

	/** events */
	public $onBeforeLoadPlugins = [];
	public $onAfterLoadPlugins = [];
	public $onBeforeStartPlugins = [];
	public $onAfterStartPlugins = [];

	public function __construct() 
	{
		$this->noPlugins = getenv('ENVIROMENT') == 'NO_PLUGINS';
	}

	public function loadConfigs(\Nette\Configurator $configurator) 
	{
		if ($this->noPlugins) { //if plugins are disable load only main
			$mainConfigPath = VENDOR_PATH . '/wame/Core/config/config.neon';
			
			if (file_exists($mainConfigPath)) {
				$configurator->addConfig($mainConfigPath);
			}
			
			return;
		}

		Debugger::timer('plugin');

		$this->tempPlugins = [];

		$pluginsFiles = [];
		
		foreach ($this->pluginDirectories as $dir) {
			$pluginsFiles = array_merge($pluginsFiles, glob($dir . '/**/' . self::COMPOSER_FILE));
		}

		foreach ($pluginsFiles as $pf) {
			$this->tempPlugins[] = new TempPlugin($pf);
		}

		$pluginSorter = new PluginSorter();
		$pluginSorter->sort($this->tempPlugins);

		foreach ($this->tempPlugins as $plugin) {
			foreach ($plugin->getConfigs() as $config) {
				$configurator->addConfig($config);
			}
		}

		$this->pluginLoadTime += Debugger::timer('plugin');
	}

	public function loadPlugins(\Nette\DI\Container $container) 
	{
		if ($this->noPlugins) {
			return;
		}

		Debugger::timer('plugin');

		$this->onBeforeLoadPlugins($this);

		foreach ($container->findByType(Plugin::class) as $pluginName) {
			$plugin = $container->getService($pluginName);
			$this->plugins[] = $plugin;
			$container->callInjects($plugin);
			$this->callPluginInjects($container, $plugin);
		}

		$pluginSorter = new PluginSorter();
		$pluginSorter->sort($this->plugins);

		foreach ($this->plugins as $plugin) {
			$plugin->onLoad($this);
		}

		$this->onAfterLoadPlugins($this);

		$this->pluginLoadTime += Debugger::timer('plugin');
	}

	public function startPlugins() 
	{
		if ($this->noPlugins) {
			return;
		}

		Debugger::timer('plugin');

		$this->onBeforeStartPlugins($this);

		foreach ($this->plugins as $plugin) {
			$plugin->onEnable($this);
		}

		$this->onAfterStartPlugins($this);

		$this->pluginLoadTime += Debugger::timer('plugin');
	}

	public function getPlugins() 
	{
		return $this->plugins;
	}

	public function getPluginLoadTime() 
	{
		return $this->pluginLoadTime;
	}

	private function callPluginInjects(\Nette\DI\Container $container, Plugin $plugin) 
	{
		$pluginMethods = array_values(array_filter(get_class_methods($plugin), function ($name) {
			return substr($name, 0, 6) === 'plugin';
		}));

		foreach ($pluginMethods as $methodName) {
			try {
				$args = \Nette\DI\Helpers::autowireArguments(\Nette\Utils\Callback::toReflection([$plugin, $methodName]), [], $container);
			} catch (\Nette\DI\ServiceCreationException $e) {
				$args = null;
			}
			
			if ($args) {
				//if all services found
				call_user_func_array([$plugin, $methodName], $args);
			}
		}
	}

	public function addDirectory($dir) 
	{
		if (is_dir($dir)) {
			$this->pluginDirectories[] = $dir;
		} else {
			throw new \Exception("Directory $dir doesnt exist.");
		}
	}

}