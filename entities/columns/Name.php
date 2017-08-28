<?php

namespace Wame\Core\Entities\Columns;


trait Name
{
    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

	
	/** get ***********************************************************************************************************/

	public function getName()
	{
		return $this->name;
	}


	/** set ***********************************************************************************************************/

	public function setName($name)
	{
		$this->name = $name;
		
		return $this;
	}
	
}
