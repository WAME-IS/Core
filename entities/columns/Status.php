<?php

namespace Wame\Core\Entities\Columns;

trait Status
{
	/**
	 * @ORM\Column(name="status", type="integer", length=1, nullable=true)
	 */
	protected $status;

	
	/** get ************************************************************/

	public function getStatus()
	{
		return $this->status;
	}


	/** set ************************************************************/

	public function setStatus($status)
	{
		$this->status = $status;
		
		return $this;
	}
	
}