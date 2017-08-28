<?php

namespace Wame\Core\Entities\Columns;


trait Main
{
    /**
	 * @ORM\Column(name="main", type="boolean")
	 */
    protected $main = 0;

	
	/** get ***********************************************************************************************************/

	public function getMain()
	{
		return $this->main;
	}


	/** set ***********************************************************************************************************/

	public function setMain($main)
	{
		$this->main = $main;
		
		return $this;
	}
	
}
