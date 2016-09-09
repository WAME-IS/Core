<?php

namespace Wame\Core\Entities\Columns;

trait EditDate
{
	/**
	 * @var DateTime
	 * @ORM\Column(name="edit_date", type="datetime", nullable=true)
	 */
	protected $editDate;

	
	/** get ************************************************************/
	
	public function getEditDate()
	{
		return $this->editDate;
	}


	/** set ************************************************************/

	public function setEditDate($editDate = null)
	{
		$this->editDate = $editDate ?: \Wame\Utils\Date::toDateTime('now');
		
		return $this;
	}
	
}