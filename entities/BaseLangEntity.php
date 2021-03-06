<?php

namespace Wame\Core\Entities;

use Doctrine\ORM\Mapping as ORM;
use Wame\LanguageModule\Entities\TranslatableEntity;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseLangEntity extends BaseEntity
{
    /**
     * Set entity
     * 
     * @param TranslatableEntity $entity    entity
     */
    abstract public function setEntity($entity);
    
}
