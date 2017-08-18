<?php

namespace Wame\Core\Entities\Columns;


trait Type
{
    /**
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    protected $type;


    /** get ***********************************************************************************************************/

    public function getType()
    {
        return $this->type;
    }


    /** set ***********************************************************************************************************/

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
	
}
