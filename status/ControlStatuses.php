<?php

namespace Wame\Core\Status;

use Nette\Object;

class ControlStatuses extends Object
{

    /**
     * Event called when value of some status object is changed
     * 
     * Parameters of event:
     * \Wame\Core\Mode\Event\ControlStatusSetEvent $event
     * 
     * @var callable[]
     */
    public $onSet = [];

}
