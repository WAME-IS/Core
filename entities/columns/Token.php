<?php

namespace Wame\Core\Entities\Columns;

trait Token
{
    /**
     * @ORM\Column(name="token", type="string", length=64, nullable=true)
     */
    protected $token = null;

}