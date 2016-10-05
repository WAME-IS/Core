<?php

namespace Wame\Core\Traits;

use DusanKasan\Knapsack\Collection;

trait TRegister
{
    /**
     * Get entity alias
     * 
     * @param BaseEntity $entity
     * @return string
     */
    public function getEntityAlias($register, $entity)
    {
        $entityName = get_class($entity);
        
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
