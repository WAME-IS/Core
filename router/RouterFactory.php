<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		
		$router[] = new Route('/', 'Homepage:Homepage:default');
		
		$router[] = new Route('[<module>/]<presenter>/<action>[/<id>]', 'Homepage:Homepage:default');
		
		return $router;
	}

}
