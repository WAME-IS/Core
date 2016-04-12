<?php

namespace Wame\Core\Entities\Columns;

trait Identifier
{
	/**
     * @ORM\Column(name="id", type="integer", length=11, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

}