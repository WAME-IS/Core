<?php

namespace Wame\Core\Entities\Columns;

trait Description
{
    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

	
	/** get ************************************************************/

	public function getDescription()
	{
		return $this->description;
	}


	/** set ************************************************************/

	public function setDescription($description)
	{
		$this->description = $description;
		
		return $this;
	}
	
}