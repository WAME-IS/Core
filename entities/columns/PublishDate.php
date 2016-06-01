<?php

namespace Wame\Core\Entities\Columns;

trait PublishDate
{
	/**
	 * @var DateTime
	 * @ORM\Column(name="publish_start_date", type="datetime", nullable=true)
	 */
	protected $publishStartDate;

	/**
	 * @var DateTime
	 * @ORM\Column(name="publish_end_date", type="datetime", nullable=true)
	 */
	protected $publishEndDate;
    
    
	/** get ************************************************************/

	public function getPublishStartDate()
	{
		return $this->publishStartDate;
	}

	public function getPublishEndDate()
	{
		return $this->publishEndDate;
	}

	
	/** set ************************************************************/

	public function setPublishStartDate($publishStartDate)
	{
		$this->publishStartDate = $publishStartDate;
		 
		return $this;
	}

	public function setPublishEndDate($publishEndDate)
	{
		$this->publishEndDate = $publishEndDate;
		 
		return $this;
	}
	
}