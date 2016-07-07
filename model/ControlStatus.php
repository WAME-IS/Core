<?php

namespace Wame\Core\Model;

use Nette\Application\UI\Control;
use Nette\Object;
use Nette\Utils\ArrayHash;

class ControlStatus extends Object
{

    /** @var Control */
    private $control;

    /** @var ArrayHash */
    private $params;

    /** @var array */
    private $listeners = [];

    public function __construct(Control $control)
    {
        $this->control = $control;
        $this->params = new ArrayHash();
    }

    /**
     * 
     * @param string $name
     * @param callable $callback
     * @return type
     */
    public function get($name, $callback = null)
    {
        $value = $this->recursiveGet($name);
        if ($callback) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = [];
            }
            $this->listeners[$name][] = $callback;
            call_user_func_array($callback, [$value]);
        } else {
            return $value;
        }
    }

    public function set($name, $value)
    {
        $this->params->$name = $value;
        $this->callListeners($name, 'force');
    }

    private function recursiveGet($name)
    {
        if (isset($this->params->$name)) {
            return $this->params->$name;
        }
        $parent = $this->control->getParent();
        if ($parent && method_exists($parent, "getStatus")) {
            return $parent->getStatus()->get($name);
        }
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
}
