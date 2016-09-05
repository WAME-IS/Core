<?php

namespace Wame\Core\Registers\Types;

abstract class StatusType implements IRegisterType
{
    use \Wame\Core\Registers\Traits\AliasTrait;
    
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
