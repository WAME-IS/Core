<?php

namespace Wame\Core\Cache;

use Nette\Caching\IStorage;
use Wame\Core\Cache\TemplatingCache;

class TemplatingCacheFactory
{

    private $cacheStorage;

    public function __construct(IStorage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
        $this->cacheStorage = new \Nette\Caching\Storages\DevNullStorage();
    }

    public function create($name = null)
    {
        return new TemplatingCache($this->cacheStorage, $name);
    }

    function setCacheStorage($cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }
}
