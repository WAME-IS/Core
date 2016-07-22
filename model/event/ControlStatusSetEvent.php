<?php

namespace Wame\Core\Mode\Event;

use Nette\Object;
use Wame\Core\Model\ControlStatus;

class ControlStatusSetEvent extends Object
{

    /** @var ControlStatus */
    private $status;

    /** @var string */
    private $name;

    /** @var mixed */
    private $value;

    public function __construct(ControlStatus $status, $name, $value)
    {
        $this->status = $status;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return ControlStatustion getStatus()
      {
      return $this->status;
      }

      /**
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $name
     * @return ControlStatusSetEvent This event
     */
    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $value
     * @return ControlStatusSetEvent This event
     */
    function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
