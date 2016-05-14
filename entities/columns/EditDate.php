<?php

namespace Wame\Core\Entities\Columns;

trait EditDate
{
	/**
	 * @ORM\Column(name="edit_date", type="datetime", nullable=true)
	 */
	protected $editDate;

	/**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="edit_user_id", referencedColumnName="id", nullable=false)
	 */
	protected $editUser;

	
	public function getEditDate()
	{
		return $this->editDate;
	}


	public function setEditDate($editDate)
	{
		$this->editDate = $editDate;
		
		return $this;
	}
	
	
	public function getEditUser()
	{
		return $this->editUser;
	}


	public function setEditUser($editUser)
	{
		$this->editUser = $editUser;
		
		return $this;
	}
	
}