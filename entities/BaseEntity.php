<?php

namespace Wame\Core\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity
{
    public function __call($name, $args) 
    {
        if ($cb = static::extensionMethod($name)) {
			/** @var \Nette\Callback $cb */
			array_unshift($args, $this);

			return call_user_func_array($cb, $args);
		}
        
        return parent::__call($name, $args);
    }
    
}
