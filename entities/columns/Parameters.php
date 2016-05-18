<?php

namespace Wame\Core\Entities\Columns;

use Nette\Neon\Neon;

trait Parameters
{
    /**
     * @ORM\Column(name="parameters", type="string", nullable=true)
     */
    protected $parameters = null;
	
	
	/** get ************************************************************/

	public function getParameters()
	{
		if ($this->parameters) {
			return Neon::decode($this->parameters);
		} else {
			return [];
		}
	}
	
	
	public function getParameter($key)
	{
		if (array_key_exists($key, $this->getParameters())) {
			return $this->getParameters()[$key];
		} else {
			return null;
		}
	}
	
	
	/** set ************************************************************/

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