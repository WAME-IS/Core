<?php

namespace Wame\Core\Traits;

use DusanKasan\Knapsack\Collection;
use Wame\Core\Entities\BaseEntity;

trait TRegister
{
    /**
     * Get entity alias
     * 
     * @param BaseEntity|string $entity
     * @return string
     */
    public function getEntityAlias($register, $entity)
    {
        $entityName = ($entity instanceof BaseEntity) ? get_class($entity) : $entity;
        
        $alias = Collection::from($register->getArray())
                ->reduce(function($tmp, $item) use($entityName) {
                    if($item['service']->getEntityName() == $entityName) {
                        $tmp = $item['service']->getAlias();
                    }
                    return $tmp;
                }, 0);
        
        return $alias;
    }
    
}
