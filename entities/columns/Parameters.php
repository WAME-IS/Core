<?php

namespace Wame\Core\Entities\Columns;

trait Parameters
{
    /**
     * @ORM\Column(name="parameters", type="neon", nullable=true)
     */
    protected $parameters = null;
	
	
	/** get ************************************************************/

	public function getParameters()
	{
		return $this->parameters;
	}
	
	
	public function getParameter($parameter)
	{
		if (array_key_exists($parameter, $this->parameters)) {
			return $this->parameters[$parameter];
		} else {
			return null;
		}
	}
	
	
	/** set ************************************************************/

	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
		return $this;
	}
    
    public function setParameter($parameter, $value)
	{
        $this->parameters[$parameter] = $value;
		return $this;
	}

}