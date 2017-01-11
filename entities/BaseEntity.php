<?php

namespace Wame\Core\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\ObjectMixin;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity extends \Kdyby\Doctrine\Entities\BaseEntity
{
    /**
     * {@inheritDoc}
     *
     * @deprecated
     */
    public function __call($name, $args) //TODO nechavame to tu? Co je toto za fix?
    {
        /** @var Callback $cb */
        if ($cb = ObjectMixin::getExtensionMethod(self::class, $name)) {
			array_unshift($args, $this);

			return call_user_func_array($cb, $args);
		}

        return parent::__call($name, $args);
    }
    
}
