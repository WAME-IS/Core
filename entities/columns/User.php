<?php

namespace Wame\Core\Entities\Columns;

trait User
{
    /**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	protected $user;

	
	/** get ************************************************************/

	public function getUser()
	{
		return $this->user;
	}


	/** set ************************************************************/

	public function setUser($user)
	{
		$this->user = $user;
		
		return $this;
	}
	
}