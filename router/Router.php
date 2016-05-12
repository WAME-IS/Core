<?php

namespace Wame\Core;

use h4kuna\Gettext\GettextSetup,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\RouteList;

class Router extends RouteList {

	public function __construct(GettextSetup $translator) {

		$this[] = new Route('index.php', 'Homepage:Homepage:default', Route::ONE_WAY);

		$this[] = new Route('[<lang ' . $translator->routerAccept() . '>/][<module>/]<presenter>/<action>/[<id>/]', [
			'lang' => $translator->getDefault(),
			'module' => 'Homepage',
			'presenter' => 'Homepage',
			'action' => 'default',
			'id' => null
		]);
	}

	/**
	 * Add new route, for simpler acces from config
	 * 
	 * @param \Nette\Application\Routers\Route $route
	 */
	public function addRoute(\Nette\Application\Routers\Route $route) {
		$this[] = new Route(NULL);
		for($i = $this->count()-1; $i>0; $i--) {
			$this->offsetSet($i, $this->offsetGet($i - 1));
		}
		$this->offsetSet(0, $route);
	}
}
