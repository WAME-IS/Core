<?php

namespace Wame\Core\Entities\Columns;

trait Lang
{
    /**
     * @ORM\Column(name="lang", type="string", length=2, nullable=true)
     */
    protected $lang;

}