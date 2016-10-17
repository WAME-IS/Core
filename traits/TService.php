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
        $serviceName = key($this->container->findByTag($entityName));
        $service = $this->container->getService($serviceName);

        if($service instanceof BaseRepository) {
            return $service;
        } else {
            throw new Exception('Repository not found.');
        }
    }
}