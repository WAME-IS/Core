<?php

namespace Wame\Core\Registers;

use Nette\InvalidArgumentException;
use Nette\Object;
use Wame\Core\Registers\Types\IRegisterType;

/**
 * BaseRegister
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
abstract class BaseRegister extends Object implements IRegister
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
     * @param object $service service
     * @param string $name name
     */
    public function add($service, $name = null)
    {
        if (!$service) {
            throw new InvalidArgumentException("Trying to insert invalid service.");
        }

        if (is_a($service, $this->type)) {

            if (!$name) {
                $name = $this->getDefaultName($service);
            }
            
            if ($service instanceof IRegisterType) {
                $service->setAlias($name);
            }

            $this->array[$name] = $service;
        } else {
            throw new InvalidArgumentException("Trying to register class " . get_class($service) . " into register of " . $this->type);
        }
    }

    /**
     * Get default name
     *
     * @param $service
     * @return string
     */
    protected function getDefaultName($service)
    {
        return get_class($service);
    }

    /**
     * Remove service from register.
     *
     * @param object|string $name name
     * @return array
     * @internal param object|string $service Service or name
     */
    public function remove($name)
    {
        if (is_object($name)) {
            return array_diff($this, [$name]);
        } else {
            unset($this[$name]);
        }
    }

    /**
     * Get all registered services
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->array;
    }

    /**
     * Get service by name
     * 
     * @param string $name
     * @return object|null Service
     */
    public function getByName($name)
    {
        if (isset($this->array[$name])) {
            return $this->array[$name];
        }
        return null;
    }

    /**
     * Returns an iterator over all items.
     * 
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator((array) $this->array);
    }

    public function offsetExists($key)
    {
        return isset($this->array[$key]);
    }

    public function offsetGet($key)
    {
        return $this->array[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->array[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->array[$key]);
    }

}
