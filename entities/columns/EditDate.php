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

}