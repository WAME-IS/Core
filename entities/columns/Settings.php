<?php

namespace Wame\Core\Entities\Columns;

use Nette\Neon\Neon;

trait Settings
{
    /**
     * @ORM\Column(name="settings", type="string", nullable=true)
     */
    protected $settings = null;
	
	
	public function getSettings()
	{
		if ($this->settings) {
			return Neon::decode($this->settings);
		} else {
			return [];
		}
	}
	
	
	public function setSettings($settings)
	{
		if (is_array($settings) && count($settings) > 0) {
			$this->settings = Neon::encode($settings);
		} else {
			$this->settings = null;
		}
		
		return $this;
	}

}