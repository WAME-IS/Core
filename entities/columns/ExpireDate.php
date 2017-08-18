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

	
	/** get ***********************************************************************************************************/

	public function getExpireDate()
	{
	    if ($this->expireDate instanceof \DateTime) {
            return $this->expireDate;
        }

		return Date::toDateTime($this->expireDate);
	}


    public function isActive()
    {
        if ($this->getExpireDate() instanceof \DateTime && $this->getExpireDate() >= Date::toDateTime(Date::NOW)) {
            return true;
        }

        return false;
    }


	/** set ***********************************************************************************************************/
	
	public function setExpireDate($expireDate = null)
	{
		$this->expireDate = $expireDate ?: Date::toDateTime('now');
		
		return $this;
	}
	
}
