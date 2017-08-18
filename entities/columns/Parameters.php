<?php

namespace Wame\Core\Entities\Columns;


trait Parameters
{
    /**
     * @ORM\Column(name="parameters", type="neon", length=512, nullable=true)
     */
    protected $parameters = null;
	
	
	/** get ***********************************************************************************************************/

	public function getParameters()
	{
        if ($this->parameters) {
            return $this->parameters;
        }

        return [];
	}
	
	
	public function getParameter($parameter)
	{
		if (array_key_exists($parameter, $this->parameters)) {
			return $this->parameters[$parameter];
		}

        return null;
	}
	
	
	/** set ***********************************************************************************************************/

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
    
    
    /** other ***********************************************************************************************************/
    
    public function hasParameter($parameter)
    {
        return array_key_exists($parameter, $this->parameters);
    }

}
