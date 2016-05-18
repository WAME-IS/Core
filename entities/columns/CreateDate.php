<?php

namespace Wame\Core\Entities\Columns;

trait CreateDate
{
	/**
	 * @var DateTime
	 * @ORM\Column(name="create_date", type="datetime", nullable=true)
	 */
	protected $createDate;

	/**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="create_user_id", referencedColumnName="id", nullable=true)
	 */
	protected $createUser;

	
	public function getCreateDate()
	{
		return $this->createDate;
	}


	public function setCreateDate($createDate)
	{
		$this->createDate = $createDate;
		
		return $this;
	}
	
	
	public function getCreateUser()
	{
		return $this->createUser;
	}


	public function setCreateUser($createUser)
	{
		$this->createUser = $createUser;
		
		return $this;
	}
	
}