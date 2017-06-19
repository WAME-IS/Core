<?php

namespace Wame\Core\Repositories\Modifiers;

use Doctrine\ORM\QueryBuilder;

trait TModifier
{
    /** @var array */
    private $modifiers = [];

    private $modifierDisabled = [];


    /**
     * Add modifier
     *
     * @param IRepositoryModifier $modifier modifier
     */
    public function addModifier(IRepositoryModifier $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    public function enableModifier($modifierClass)
    {
        if(($key = array_search($modifierClass, $this->modifierDisabled)) !== false) {
            unset($this->modifierDisabled[$key]);
        }
    }

    public function enableAllModifier()
    {
        $this->modifierDisabled = [];
    }

    public function disableModifier($modifierClass)
    {
        $this->modifierDisabled[] = $modifierClass;
    }

    public function disableAllModifier()
    {

    }

    /**
     * Apply modifier
     *
     * @param QueryBuilder $qb query builder
     * @param string $alias
     */
    private function applyModifiers(QueryBuilder $qb, string $alias)
    {
        foreach($this->modifiers as $modifier) {
            if(in_array(get_class($modifier), $this->modifierDisabled)) continue;
            $modifier($qb, $alias);
        }
    }

}