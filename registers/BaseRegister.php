<?php

namespace Wame\Core\Registers;

use Nette\InvalidArgumentException;
use Nette\Object;
use Nette\Reflection\ClassType;
use Nette\Utils\ArrayHash;
use RecursiveArrayIterator;

/**
 * BaseRegister
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class BaseRegister extends Object implements IRegister
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
    public function add($service, $name = null)
    {
        if (!$service) {
            throw new InvalidArgumentException("Trying to insert invalid service.");
        }

        if ((new ClassType(get_class($service)))->is($this->type)) {

            if (!$name) {
                $name = get_class($service);
            }

            $this->array[$name] = $service;
        } else {
            throw new InvalidArgumentException("Trying to register class " . get_class($service) . " into register of " . $this->type);
        }
    }

    /**
     * Remove service from register.
     * 
     * @param object|string $service Service or name
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
     * Get all registred services
     * 
     * @return ArrayHash
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
     * @return RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator((array) $this->array);
    }
}
