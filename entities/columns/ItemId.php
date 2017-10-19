<?php

namespace Wame\Core\Entities\Columns;


trait ItemId
{
    /**
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    protected $itemId;


    /** get ************************************************************/

    public function getItemId()
    {
        return $this->itemId;
    }


    /** set ************************************************************/

    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }
	
}
