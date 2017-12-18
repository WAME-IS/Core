<?php

namespace Wame\Core\Entities\Columns;


trait Url
{
    /**
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    protected $url;


    /** get ************************************************************/

    public function getUrl()
    {
        return $this->url;
    }


    /** set ************************************************************/

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
	
}
