<?php

namespace Wame\Core\Registers;

use Wame\Core\Registers\Types\StatusType;

class StatusTypeRegister extends PriorityRegister
{
    public function __construct()
    {
        parent::__construct(StatusType::class);
    }
    
}
