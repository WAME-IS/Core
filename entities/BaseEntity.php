<?php

namespace Wame\Core\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity 
{
	
	protected function sortLangs($langs)
	{
		$arr = [];
		
		foreach ($langs as $lang) {
			$arr[$lang->lang] = $lang;
		}
		
		return $arr;
	}
}
