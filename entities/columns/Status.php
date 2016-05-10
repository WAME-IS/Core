<?php

namespace Wame\Core\Entities\Columns;

trait Status
{
	/**
	 * @ORM\Column(name="status", type="integer", length=1, nullable=true)
	 */
	protected $status;

	
	public function getStatus()
	{
		return $this->status;
	}


	public function setStatus($status)
	{
		$this->status = $status;
		
		return $this;
	}
	
}