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
	public static function createRouter(\h4kuna\Gettext\GettextSetup $translator)
	{
		$router = new RouteList();
		
		$router[] = new Route('index.php', 'Homepage:Homepage:default', Route::ONE_WAY);
		
		$router[] = new Route('[<lang ' . $translator->routerAccept() . '>/][<module>/]<presenter>/<action>/[<id>/]', [
			'module' => 'Homepage',
			'presenter' => 'Homepage',
			'action' => 'default',
			'id' => 'null',
			'lang' => $translator->getDefault()
		]);

		return $router;
	}

}
