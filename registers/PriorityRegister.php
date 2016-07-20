<?php

namespace Wame\Core\Registers;

use Nette\Reflection\ClassType;
use RecursiveArrayIterator;
use WebLoader\InvalidArgumentException as InvalidArgumentException2;

class PriorityRegister implements IRegister
{

    /** @var string Type */
    private $type;

    /** @var array */
    private $array;

    /**
     * @param string $type Name of class accepted in this register
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->array = [];
    }

    /**
     * Register service into register.
     * 
     * @param object $service
     * @param string $name
     */
    public function add($service, $name = null, $priority = 0)
    {
        if (!$service) {
            throw new InvalidArgumentException2("Trying to insert invalid service.");
        }

        if ((new ClassType(get_class($service)))->is($this->type)) {

            if (!$name) {
                $name = $this->getDefaultName($service);
            }

            $index = $this->getIndexByName($name);
            if ($index >= 0) {
                $this->array[$index]['service'] = $service;
                $this->array[$index]['priority'] = $priority;
            } else {
                $this->array[] = ['name' => $name, 'service' => $service, 'priority' => $priority];
            }

            usort($this->array, function($s1, $s2) {
                return $s2['priority'] - $s1['priority'];
            });
        } else {
            throw new InvalidArgumentException2("Trying to register class " . get_class($service) . " into register of " . $this->type);
        }
    }
    
    protected function getDefaultName($service)
    {
        return get_class($service);
    }

    /**
     * Remove service from register.
     * 
     * @param object|string $name Service or name
     */
    public function remove($name)
    {
        if (is_object($name)) {
            foreach ($this as $name => $service) {
                if ($service['service'] == $name) {
                    unset($this[$name]);
                    break;
                }
            }
        } else {
            unset($this[$name]);
        }
    }

    /**
     * Get index by name
     * @param string $name
     * @return int Index of service or -1 if not found
     */
    public function getIndexByName($name)
    {
        for ($i = 0; $i < count($this->array); $i++) {
            if ($this->array[$i]['name'] == $name) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * Get service by name
     * 
     * @param string $name
     * @return object|null Service
     */
    public function getByName($name)
    {
        foreach ($this->array as $service) {
            if ($service['name'] == $name) {
                return $service['service'];
            }
        }
        return null;
    }

    /**
     * 
     * @param string $name
     * @return int Priority
     */
    public function getPriority($name)
    {
        if (isset($this->array[$name])) {
            return $this->array[$name]['priority'];
        }
        return null;
    }

    /**
     * Get all registred services
     * @return array
     */
    public function getAll()
    {
        return array_map(function($s) {
            return $s['service'];
        }, $this->array);
    }

    /**
     * Returns an iterator over all items.
     * @return RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new RecursiveArrayIterator($this->getAll());
    }

    public function offsetExists($key)
    {
        return $this->getByName($key) != null;
    }

    public function offsetGet($key)
    {
        return $this->getByName($key);
    }

    public function offsetSet($key, $value)
    {
        foreach ($this->array as $service) {
            if ($service['name'] == $key) {
                $service['service'] = $value;
                break;
            }
        }
    }

    public function offsetUnset($key)
    {
        foreach ($this->array as $index => $service) {
            if ($service['name'] == $key) {
                unset($this->array[$index]);
                break;
            }
        }
    }
}
