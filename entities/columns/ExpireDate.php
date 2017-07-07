<?php

namespace Wame\Core\Entities\Columns;

use Wame\Utils\Date;

trait ExpireDate
{
	/**
	 * @var \DateTime
	 * @ORM\Column(name="expire_date", type="datetime", nullable=true)
	 */
	protected $expireDate;

	
	/** get ************************************************************/

	public function getExpireDate()
	{
		return $this->expireDate;
	}


	/** set ************************************************************/
	
	public function setExpireDate($expireDate = null)
	{
		$this->expireDate = $expireDate ?: Date::toDateTime('now');
		
		return $this;
	}
	
}