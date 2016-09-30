<?php

namespace Wame\Core\Doctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zenify\DoctrineFilters\Contract\ConditionalFilterInterface;


final class SoftdeletableFilter implements ConditionalFilterInterface
{
    /** @var boolean */
    private $enabled = true;
    
    
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $entity, $alias)
    {
        if ($entity->getReflectionClass()->hasProperty('status') && $this->enabled) {
            return sprintf('%s.status != %s', $alias, 0);
        }
        
        return "";
    }

    
    public function setEnabled($status)
    {
        $this->enabled = $status;
        
        return $this;
    }
    
    
    public function isDisabled()
    {
        $this->enabled = false;
        
        return $this;
    }
    
    
    public function isEnabled() 
    {
        return $this->enabled;
    }
    
}
