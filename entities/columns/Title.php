<?php

namespace Wame\Core\Entities\Columns;


trait Title
{
    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

	
	/** get ***********************************************************************************************************/

	public function getTitle()
	{
		return $this->title;
	}


	/** set ***********************************************************************************************************/

	public function setTitle($title)
	{
		$this->title = $title;
		
		return $this;
	}
	
}
