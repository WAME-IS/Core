<?php

namespace App\Core\Model;

use Nette\Object;
use Nette\DI\Container;

class ServicesLoader extends Object 
{
	use InlineCache;

	/** @var Container */
	private $container;

	/** @var array */
	private $customServices = [];

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function getServices($type)
	{
		return $this->inlineCache($type, function() use ($type) {
			$serviceNames = $this->container->findByType($type);

			$services = [];
			
			foreach ($serviceNames as $serviceName) {
				$services[] = $this->container->getService($serviceName);
			}

			if (isset($this->customServices[$type])) {
				$services = array_merge($services, $this->customServices[$type]);
			}
			
			return $services;
		});
	}

	public function addCustomService($type, $service)
	{
		if (!array_key_exists($type, $this->customServices)) {
			$this->customServices[$type] = [];
		}
		
		$this->customServices[$type][] = $service;
		$this->clearInlineCache($type);
	}

	public function getRepository($name)
	{
		foreach ($this->getServices(\App\Main\Model\MyBaseRepository::class) as $repository) {
			if ($repository->getName() == $name) {
				return $repository;
			}
		}
	}

}
