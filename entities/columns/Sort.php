<?php

namespace Wame\Core\Entities\Columns;

trait Sort
{
    /**
	 * @ORM\Column(name="sort", type="integer", nullable=false)
	 */
	protected $sort;

	
	public function getSort()
	{
		return $this->sort;
	}


	public function setSort($sort)
	{
		$this->sort = $sort;
		
		return $this;
	}
	
}