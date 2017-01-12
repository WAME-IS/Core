<?php

namespace Wame\Core\Registers;

interface IRegister extends \IteratorAggregate, \ArrayAccess
{
    /**
     * Register service into register.
     * 
     * @param object $service
     * @param string $name
     */
    public function add($service, $name = null);
    
    /**
     * Remove service from register.
     * 
     * @param object|string $service Service or name
     */
    public function remove($service);
    
    /**
     * Get all registred services
     * 
     * @return array
     */
    public function getAll();
    
    /**
     * Get service by name
     * 
     * @param string $name
     * @return object Service
     */
    public function getByName($name);

}
