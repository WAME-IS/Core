<?php

namespace Wame\Core\Doctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zenify\DoctrineFilters\Contract\FilterInterface;

final class SoftdeletableFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, $alias)
    {
        if($entity->getReflectionClass()->hasProperty('status')) {
            return "$alias.status != 0";
        }
        
        return "";
    }
    
}
