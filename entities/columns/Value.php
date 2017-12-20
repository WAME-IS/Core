<?php

namespace Wame\Core\Entities\Columns;


trait Value
{
    /**
     * @ORM\Column(name="value_id", type="string", nullable=true)
     */
    protected $value;


    /** get ************************************************************/

    public function getValue()
    {
        return $this->value;
    }


    /** set ************************************************************/

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
	
}
