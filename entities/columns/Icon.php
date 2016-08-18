<?php

namespace Wame\Core\Entities\Columns;

trait Icon
{
    /**
     * @ORM\Column(name="icon", type="string", nullable=true)
     */
    protected $icon;

	
	/** get ************************************************************/

	public function getIcon()
	{
		return $this->icon;
	}


	/** set ************************************************************/

	public function setIcon($icon)
	{
		$this->icon = $icon;
		
		return $this;
	}
	
}