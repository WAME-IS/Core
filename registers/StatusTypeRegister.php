<?php

namespace Wame\Core\Registers;

use Wame\Core\Registers\Types\StatusType;

class StatusTypeRegister extends PriorityRegister
{
    public function __construct()
    {
        parent::__construct(StatusType::class);
    }


    /**
     * Get by entity class
     *
     * @param string $class
     * @return mixed
     */
    public function getByEntityClass($class)
    {
        foreach ($this->array as $service) {
            if ($service['service']->getEntityName() == $class) {
                return $service['service'];
            }
        }
        return null;
    }

}
