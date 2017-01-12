<?php

namespace Wame\Core\Cache;

use Nette\Caching\IStorage;
use Nette\Caching\Storages\DevNullStorage;

class TemplatingCacheFactory
{
    /** @var DevNullStorage */
    private $cacheStorage;


    public function __construct(IStorage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
        $this->cacheStorage = new DevNullStorage();
    }


    /**
     * Create
     *
     * @param string $name name
     * @return TemplatingCache
     */
    public function create($name = null)
    {
        return new TemplatingCache($this->cacheStorage, $name);
    }

    /**
     * Set cache storage
     *
     * @param DevNullStorage $cacheStorage cache storage
     */
    public function setCacheStorage($cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

}
