<?php

namespace Core;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\CliRouter;

class RouterFactory
{
	/** @var \Nette\DI\Container */
	private $container;
	
	public function __construct(\Nette\DI\Container $container)
	{
		$this->container = $container;
	}
	
	/**
	 * @return Nette\Application\IRouter
	 */
	public function createRouter(\h4kuna\Gettext\GettextSetup $translator)
	{
		$router = new RouteList();
		
		$router[] = new Route('index.php', 'Homepage:Homepage:default', Route::ONE_WAY);
		
//		if ($this->container->parameters['consoleMode']) {
//			$router[] = new CliRouter(array('lang' => 'en', 'action' => 'ImportExport:default'));
//			
//			$router[] = new Route('<lang>/<presenter>/<action>[/<id>][/<alias>]', [
//				'lang' => 'en',
//				'presenter' => 'Homepage',
//				'action' => 'default',
//				'id' => null
//			]);
//		} else {
			$router[] = new Route('admin/<lang ' . $translator->routerAccept() . '>/<presenter>/<action>/<id>', [
				'lang' => $translator->getDefault(),
				'module' => 'Admin',
				'presenter' => 'Dashboard',
				'action' => 'default',
				'id' => null
			]);
			
			$router[] = new Route('[<lang ' . $translator->routerAccept() . '>/][<module>/]<presenter>/<action>/[<id>/]', [
				'lang' => $translator->getDefault(),
				'module' => 'Homepage',
				'presenter' => 'Homepage',
				'action' => 'default',
				'id' => null
			]);
//		}

		return $router;
	}

}
