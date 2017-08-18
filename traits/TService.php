<?php

namespace Wame\Core\Traits;

use Exception;
use Wame\Core\Repositories\BaseRepository;


trait TService
{
    /**
     * Get repository by entity name
     *
     * @param $entityName
     * @return mixed
     * @throws Exception
     */
    public function getRepositoryByEntityName($entityName)
    {
        $container = null;

        if (isset($this->container)) {
            $container = $this->container;
        } elseif (isset($this->context)) {
            $container = $this->context;
        }

        if (!$container) {
            throw new Exception('Missed Nette\DI\Container for Wame\Core\Traits\TService::getRepositoryByEntityName().');
        }

        $serviceName = key($container->findByTag($entityName));
        $service = $container->getService($serviceName);

        if ($service instanceof BaseRepository) {
            return $service;
        } else {
            throw new Exception('Repository not found.');
        }
    }

}
