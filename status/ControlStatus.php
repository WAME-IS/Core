<?php

namespace Wame\Core\Status;

use Nette\Application\UI\Control;
use Nette\Object;
use Nette\Utils\ArrayHash;
use Wame\Core\Status\Event\ControlStatusSetEvent;

class ControlStatus extends Object
{
    /** @var Control */
    private $control;

    /** @var ControlStatuses */
    private $controlStatuses;

    /** @var ArrayHash */
    private $params;

    /** @var array */
    private $listeners = [];


    public function __construct(Control $control, ControlStatuses $controlStatuses)
    {
        $this->control = $control;
        $this->controlStatuses = $controlStatuses;
        $this->params = new ArrayHash();
    }


    /**
     * Get
     *
     * @param string $name
     * @param callable $callback
     * @return mixed
     */
    public function get($name, $callback = null)
    {
        $value = $this->recursiveGet($name);
        if ($callback) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = [];
            }
            $this->listeners[$name][] = $callback;
            if ($value) {
                call_user_func_array($callback, [$value]);
            }
        } else {
            return $value;
        }
    }

    /**
     * Set
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $event = new ControlStatusSetEvent($this, $name, $value);
        $this->controlStatuses->onSet($event);

        $name = $event->getName();
        $value = $event->getValue();

        $this->params->$name = $value;
        $this->callListeners($name, 'force');
    }

    /**
     * @internal
     */
    public function callListeners($name = null, $children = true)
    {
        if (!$name) {
            foreach ($this->listeners as $name => $listeners) {
                $value = $this->recursiveGet($name);
                if ($value && isset($this->listeners[$name])) {
                    foreach ($this->listeners[$name] as $callback) {
                        call_user_func_array($callback, [$value]);
                    }
                }
            }
        } else {
            $value = $this->recursiveGet($name);
            if ($value && isset($this->listeners[$name])) {
                foreach ($this->listeners[$name] as $callback) {
                    call_user_func_array($callback, [$value]);
                }
            }
        }

        if (($children && !isset($this->params->$name)) || $children == 'force') {
            foreach ($this->control->getComponents() as $component) {
                if (method_exists($component, "getStatus")) {
                    $component->getStatus()->callListeners($name);
                }
            }
        }
    }

    /**
     * Get all local
     *
     * @return ArrayHash
     */
    public function getAllLocal()
    {
        return $this->params;
    }


    /**
     * Recursive get
     *
     * @param string $name name
     * @return mixed
     */
    private function recursiveGet($name)
    {
        if (isset($this->params->$name)) {
            $value = $this->params->$name;
            if(is_callable($value)) {
                $value = call_user_func($value);
                $this->params->$name = $value;
            }
            return $value;
        }
        $parent = $this->control->getParent();
        if ($parent && method_exists($parent, "getStatus")) {
            return $parent->getStatus()->get($name);
        }
    }

}
