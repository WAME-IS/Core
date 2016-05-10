<?php

namespace Wame\Core\Entities\Columns;

use Nette\Neon\Neon;

trait Parameters
{
    /**
     * @ORM\Column(name="parameters", type="string", nullable=true)
     */
    protected $parameters = null;
	
	
	public function getParameters()
	{
		if ($this->parameters) {
			return Neon::decode($this->parameters);
		} else {
			return [];
		}
	}
	
	
	public function setParameters($parameters)
	{
		if (is_array($parameters) && count($parameters) > 0) {
			$this->parameters = Neon::encode($parameters);
		} else {
			$this->parameters = null;
		}
		
		return $this;
	}

}