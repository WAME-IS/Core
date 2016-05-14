<?php

namespace Wame\Core\Entities\Columns;

trait Lang
{
    /**
     * @ORM\Column(name="lang", type="string", length=2, nullable=true)
     */
    protected $lang;

	
	public function getLang()
	{
		return $this->lang;
	}


	public function setLang($lang)
	{
		$this->lang = $lang;
		
		return $this;
	}
	
}