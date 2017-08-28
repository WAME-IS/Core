<?php

namespace Wame\Core\Entities\Columns;


trait Sort
{
    /**
	 * @ORM\Column(name="sort", type="integer", nullable=false)
	 */
	protected $sort = 0;


	/** get ***********************************************************************************************************/

	public function getSort()
	{
		return $this->sort;
	}
    

	/** set ***********************************************************************************************************/

	public function setSort($sort)
	{
		$this->sort = $sort;
		
		return $this;
	}
	
}
