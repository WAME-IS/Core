<?php

namespace Wame\Core\Entities\Columns;

trait CreateDate
{
	/**
	 * @ORM\Column(name="create_date", type="datetime", nullable=true)
	 */
	protected $createDate;

	/**
	 * @ORM\ManyToOne(targetEntity="\Wame\UserModule\Entities\UserEntity")
	 * @ORM\JoinColumn(name="create_user_id", referencedColumnName="id", nullable=false)
	 */
	protected $createUser;

}