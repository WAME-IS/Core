<?php

namespace Wame\Core\Repositories\Modifiers;

use Doctrine\ORM\QueryBuilder;

interface IRepositoryModifier
{
    public function __invoke(QueryBuilder $qb, string $alias);
}