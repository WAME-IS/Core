<?php

namespace Wame\Core\Models;

class PluginSorter extends DependencySorter 
{	  
	protected function getItemDependencies($item) 
	{
		return array_keys($item->getDependencies());
	}
	
	protected function getAllItemDependencies($item) 
	{
		return array_keys($item->getAllDependencies());
	}
	
}

