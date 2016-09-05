<?php

namespace Wame\Core\Registers\Traits;

trait AliasTrait
{
    /** @var string */
    private $alias;
    
    
    /**
     * Set alias
     * 
     * @param string $alias alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    
    /**
     * Get alias
     * 
     * @return string   alias
     */
    public function getAlias()
    {
        return $this->alias;
    }
    
}
