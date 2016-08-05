<?php

namespace Wame\Core\Registers\Types;

class StatusType implements IRegisterType
{
    /** @var string */
    private $alias;
    
    
    /** {@inheritDoc} */
    public function getAlias()
    {
        $this->alias;
    }

    /** {@inheritDoc} */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    
}
