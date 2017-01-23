<?php

namespace Wame\Core\Doctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zenify\DoctrineFilters\Contract\ConditionalFilterInterface;

final class SoftdeletableFilter implements ConditionalFilterInterface
{
    /** @var bool */
    private $enabled = true;
    
    
    /** {@inheritdoc} */
    public function addFilterConstraint(ClassMetadata $entity, string $alias) : string
    {
        if ($entity->getReflectionClass()->hasProperty('status') && $this->enabled) {
            return sprintf('%s.status != %s', $alias, 0);
        }
        
        return "";
    }


    /**
     * Set enabled
     *
     * @param bool $status status
     * @return $this
     */
    public function setEnabled($status)
    {
        $this->enabled = $status;
        
        return $this;
    }

    /**
     * Disable
     *
     * @return $this
     */
    public function isDisabled()
    {
        $this->enabled = false;
        
        return $this;
    }
    
    /** {@inheritdoc} */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }
    
}
