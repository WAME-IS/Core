<?php

namespace Wame\Core\Registers\Types;

abstract class StatusType implements IRegisterType
{
    /** @var string */
    private $alias;
    
    
    /** {@inheritDoc} */
    public function getAlias()
    {
        return $this->alias;
    }

    /** {@inheritDoc} */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    
    /**
     * @deprecated
     */
    public function getStatusName() //TODO remove when not used
    {
        return $this->getAlias();
    }
    
    /**
     * @return string Name of entity
     */
    public abstract function getEntityName();
    
}
