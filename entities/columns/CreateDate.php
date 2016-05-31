<?php

namespace Wame\Core\Entities\Columns;

trait CreateDate
{
	/**
	 * @var DateTime
	 * @ORM\Column(name="create_date", type="datetime", nullable=true)
	 */
	protected $createDate;

	
	/** get ************************************************************/

	public function getCreateDate()
	{
		return $this->createDate;
	}


	/** set ************************************************************/
	
	public function setCreateDate($createDate)
	{
		$this->createDate = $createDate;
		
		return $this;
	}
	
}