<?php

namespace Wame\Core\Entities\Columns;


trait CreateUser
{
	/**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="create_user_id", referencedColumnName="id", nullable=true)
	 */
	protected $createUser;

	
	/** get ***********************************************************************************************************/

	public function getCreateUser()
	{
		return $this->createUser;
	}


	/** set ***********************************************************************************************************/
	
	public function setCreateUser($createUser)
	{
		$this->createUser = $createUser;
		
		return $this;
	}
	
}
