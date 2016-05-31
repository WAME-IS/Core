<?php

namespace Wame\Core\Entities\Columns;

trait EditUser
{
	/**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="edit_user_id", referencedColumnName="id", nullable=false)
	 */
	protected $editUser;

	
	/** get ************************************************************/

	public function getEditUser()
	{
		return $this->editUser;
	}


	/** set ************************************************************/
	
	public function setEditUser($editUser)
	{
		$this->editUser = $editUser;
		
		return $this;
	}
	
}