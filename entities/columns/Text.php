<?php

namespace Wame\Core\Entities\Columns;

trait Text
{
    /**
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    protected $text;

	
	/** get ************************************************************/

	public function getText()
	{
		return $this->text;
	}


	/** set ************************************************************/

	public function setText($text)
	{
		$this->text = $text;
		
		return $this;
	}
	
}