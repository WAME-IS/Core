<?php

namespace Wame\Core\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity 
{
	/**
	 * Get languages
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
	
	
	/**
	 * Sort by languages
	 * 
	 * @param type $langs
	 * @return type
	 */
	protected function sortLangs($langs)
	{
		$arr = [];
		
		foreach ($langs as $lang) {
			$arr[$lang->lang] = $lang;
		}
		
		return $arr;
	}
	
	
	/**
	 * Add lang
	 * 
	 * @param string $lang
	 * @param object $entity
	 * @return \Wame\Core\Entities\BaseEntity
	 */
	public function addLang($lang, $entity)
	{
		$this->langs[$lang] = $entity;
		
		return $this;
	}
	
}
