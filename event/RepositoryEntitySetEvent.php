<?php

namespace Wame\Core\Event;

use Nette\Object;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 * 
 * @method string getEntityName()
 * @method void setEntityName(string $entityName)
 */
class RepositoryEntitySetEvent extends Object
{
    /** @var string */
    public $entityName;


    public function __construct($entityName)
    {
        $this->entityName = $entityName;
    }

}
