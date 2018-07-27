<?php

namespace Wame\Core\Entities\Columns;


trait SeenDate
{
    /**
     * @var DateTime
     * @ORM\Column(name="seen_date", type="datetime", nullable=true)
     */
    protected $seenDate;


    /** get *******************************************************************/

    public function getSeenDate()
    {
        return $this->seenDate;
    }


    /** set *******************************************************************/

    public function setSeenDate($seenDate)
    {
        $this->seenDate = $seenDate;

        return $this;
    }

}
