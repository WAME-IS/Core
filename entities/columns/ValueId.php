<?php

namespace Wame\Core\Entities\Columns;


trait ValueId
{
    /**
     * @ORM\Column(name="value_id", type="integer", nullable=true)
     */
    protected $valueId;


    /** get ************************************************************/

    public function getValueId()
    {
        return $this->valueId;
    }


    /** set ************************************************************/

    public function setValueId($valueId)
    {
        $this->valueId = $valueId;

        return $this;
    }
	
}
