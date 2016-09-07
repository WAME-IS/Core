<?php

namespace Wame\Core\Registers;

use Nette\InvalidArgumentException;
use RecursiveArrayIterator;
use Wame\Core\Registers\Types\IRegisterType;
use Wame\Utils\Strings;
use WebLoader\InvalidArgumentException as InvalidArgumentException2;

class PriorityRegister implements IRegister
{

    /** @var string Type */
    private $type;

    /** @var array */
    protected $array;

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
    public function add($service, $name = null, $parameters = [])
    {
        $defaultParameters = [
            'priority' => 0,
            'domain' => null
        ];

        if (!is_array($parameters)) {
            throw new InvalidArgumentException("Third parameter in register " . get_class($this) . " has to be array of parameters. '" . $parameters . "' given.");
        }

        $parameters = array_merge($defaultParameters, $parameters);

        if (!$service) {
            throw new InvalidArgumentException2("Trying to insert invalid service.");
        }

        if (is_a($service, $this->type)) {

            if (!$name) {
                $name = $this->getDefaultName($service);
            }

            if ($service instanceof IRegisterType) {
                $service->setAlias($name);
            }

            $index = $this->getIndexByName($name);
            if ($index >= 0) { // edit
                $this->array[$index]['service'] = $service;
                $this->array[$index]['parameters'] = array_merge($this->array[$index]['parameters'], $parameters);
            } else { // create
                $this->array[] = ['name' => $name, 'service' => $service, 'parameters' => $parameters];
            }

            $this->resort();
        } else {
            throw new InvalidArgumentException2("Trying to register class " . get_class($service) . " into register of " . $this->type);
        }
    }

    protected function getDefaultName($service)
    {
        return Strings::getClassName($service);
    }

    /**
     * Remove service from register.
     * 
     * @param object|string $name Service or name
     */
    public function remove($name)
    {
        if (is_object($name)) {
            foreach ($this->array as $index => $service) {
                if ($service['service'] == $name) {
                    unset($this[$index]);
                    break;
                }
            }
        } else {
            unset($this[$name]);
        }
        $this->resort();
    }

    private function resort()
    {
        usort($this->array, function($s1, $s2) {
            return $s2['parameters']['priority'] - $s1['parameters']['priority'];
        });
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
     * Get by domain
     * @param string $domain
     * @return array
     */
    public function getByDomain($domain)
    {
        return array_map(function($s) {
            return $s['service'];
        }, array_filter($this->array, function($s) use($domain) {
                return $s['parameters']['domain'] == $domain || $s['parameters']['domain'] == null;
            }
        ));
    }

    /**
     * Get parameter
     * 
     * @param string $serviceName   service name
     * @return array
     */
    public function getParameter($serviceName)
    {
        foreach ($this->array as $item) {
            if ($item['name'] == $serviceName) {
                return $item['parameters'];
            }
        }
    }

    public function getArray()
    {
        return $this->array;
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
