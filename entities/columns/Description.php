<?php

namespace Wame\Core\Entities\Columns;

trait Description
{
    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    protected $description;

	
	public function getDescription()
	{
		return $this->description;
	}


	public function setDescription($description)
	{
		$this->description = $description;
		
		return $this;
	}
	
}