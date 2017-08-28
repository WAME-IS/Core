<?php

namespace Wame\Core\Entities\Columns;


trait Template
{
    /**
     * @ORM\Column(name="template", type="string", nullable=true)
     */
    protected $template;


    /** get ***********************************************************************************************************/

    public function getTemplate()
    {
        return $this->template;
    }


    /** set ***********************************************************************************************************/

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
	
}
