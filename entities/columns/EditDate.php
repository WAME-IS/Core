<?php

namespace Wame\Core\Entities\Columns;

trait EditDate
{
	/**
	 * @var DateTime
	 * @ORM\Column(name="edit_date", type="datetime", nullable=true)
	 */
	protected $editDate;

	/**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="edit_user_id", referencedColumnName="id", nullable=false)
	 */
	protected $editUser;

	
	/** get ************************************************************/
	
	public function getEditDate()
	{
		return $this->editDate;
	}

	public function getEditUser()
	{
		return $this->editUser;
	}


	/** set ************************************************************/

	public function setEditDate($editDate)
	{
		$this->editDate = $editDate;
		
		return $this;
	}
	
	public function setEditUser($editUser)
	{
		$this->editUser = $editUser;
		
		return $this;
	}
	
}