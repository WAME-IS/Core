<?php

namespace Wame\Core\Entities\Columns;

trait Title
{
    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

	
	public function getTitle()
	{
		return $this->title;
	}


	public function setTitle($title)
	{
		$this->title = $title;
		
		return $this;
	}
	
}