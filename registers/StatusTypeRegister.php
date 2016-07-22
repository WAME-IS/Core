<?php

namespace Wame\Core\Registers;

use Wame\Core\Registers\Types\IStatusType;

class StatusTypeRegister extends PriorityRegister
{

    public function __construct()
    {
        parent::__construct(IStatusType::class);
    }
}
