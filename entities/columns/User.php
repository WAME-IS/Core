<?php

namespace Wame\Core\Entities\Columns;

trait User
{
    /**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	protected $user;

	
	public function getUser()
	{
		return $this->user;
	}


	public function setUser($user)
	{
		$this->user = $user;
		
		return $this;
	}
	
}