<?php

namespace Wame\Core\Entities;

use Wame\LanguageModule\Entities\TranslatableEntity;


trait Entity
{
	public function setEntity(TranslatableEntity $entity)
	{
		$this->description = $description;
		
		return $this;
	}
	
}
