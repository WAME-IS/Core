<?php

namespace Wame\Core\Registers\Types;

abstract class StatusType implements IRegisterType
{
    use \Wame\Core\Registers\Traits\AliasTrait;
    
    /**
     * @return string Name of entity
     */
    public abstract function getEntityName();
    
}
