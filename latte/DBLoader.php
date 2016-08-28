<?php

namespace Wame\Core;

use Latte;

class DBLoader implements Latte\ILoader
{
    /** {@inheritDoc} */
    public function getChildName($content, $parent = NULL)
    {
        return $content;
    }

    /** {@inheritDoc} */
    public function getContent(Repositories\BaseRepository $repository, $criteria = [])
    {
        $entity = $repository->get($criteria);
        return $entity->getTemplate();
    }

    /** {@inheritDoc} */
    public function isExpired($name, $time)
    {
        return FALSE;
    }
    
}
