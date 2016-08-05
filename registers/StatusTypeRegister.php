<?php

namespace Wame\Core\Registers;

class StatusTypeRegister extends PriorityRegister
{
    public function __construct()
    {
        parent::__construct(Types\StatusType::class);
    }
    
}
