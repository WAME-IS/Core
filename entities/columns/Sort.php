<?php

namespace Wame\Core\Entities\Columns;

trait Sort
{
    /**
	 * @ORM\Column(name="sort", type="integer", nullable=false)
	 */
	protected $sort = 0;

	
    /**
     * Get sort
     * 
     * @return integer
     */
	public function getSort()
	{
		return $this->sort;
	}
    
    /**
     * Set sort
     * 
     * @param integer $sort
     * @return \Wame\Core\Entities\Columns\Sort
     */
	public function setSort($sort)
	{
		$this->sort = $sort;
		
		return $this;
	}
	
}