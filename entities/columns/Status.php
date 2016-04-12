<?php

namespace Wame\Core\Entities\Columns;

trait Status
{
	/**
	 * @ORM\Column(name="status", type="integer", length=1, nullable=true)
	 */
	protected $status;

}