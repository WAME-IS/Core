<?php

namespace Wame\Core\Registers\Types;

interface IRegisterType
{
    /**
     * Set alias
     * 
     * @param string $alias alias
     */
    public function setAlias($alias);
    
    /**
     * Get alias
     * 
     * @return string   alias
     */
    public function getAlias();
    
}
