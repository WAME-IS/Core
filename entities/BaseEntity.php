<?php

namespace Wame\Core\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity 
{
	/**
	 * Sort by languages
	 * 
	 * @return array
	 */
	public function getLangs() 
	{
		$return = [];
		
		foreach ($this->langs as $lang) {
			$return[$lang->lang] = $lang;
		}
		
		return $return;
	}
	
	protected function sortLangs($langs)
	{
		$arr = [];
		
		foreach ($langs as $lang) {
			$arr[$lang->lang] = $lang;
		}
		
		return $arr;
	}
}
