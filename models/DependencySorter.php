<?php

namespace Wame\Core\Models;

class DependencySorter 
{
	private $items;

	public function sort(&$items) 
	{
		if ($this->items) {
			throw new Exception2('You have to create new instance of PluginSorter.');
		}
		
		$this->items = $items;

		usort($items, $this->getDependencySorter());
	}

	public function getDependencySorter() 
	{
		return function($p1, $p2) {
			$allDependencies = [];
			
			$this->getAllDependencies($p1, $allDependencies);

			if (in_array($p2->getName(), $allDependencies)) {
				return 1;
			}
			
			return 0;
		};
	}

	protected function getAllDependencies($item, &$allDepends, $referenceItems = []) 
	{
		$referenceItems[] = $item->getName();
		$depends = $this->getAllItemDependencies($item);

		foreach ($depends as $dep) {
			if (in_array($dep, $referenceItems)) {
				throw new \Exception('Cyclic dependency reference detected for item ' . $dep . ' from ' . $item->getName() . '!');
			} else {
				$allDepends[] = $dep;
			}

			$depItem = $this->getItemByName($dep);
			
			if ($depItem) {
				$this->getAllDependencies($depItem, $allDepends, $referenceItems);
			} else {
				if (in_array($dep, $this->getItemDependencies($item))) { //ignore soft required
					throw new \Exception('Missing dependency item ' . $dep . ' for ' . $item->getName() . '!');
				}
			}
		}
	}

	private function getItemByName($name) 
	{
		foreach ($this->items as $item) {
			if ($item->getName() == $name) {
				return $item;
			}
		}
	}

	protected function getItemDependencies($item) 
	{
		return $item->getDependencies();
	}

	protected function getAllItemDependencies($item) 
	{
		return $item->getDependencies();
	}

}