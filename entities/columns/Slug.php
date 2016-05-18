<?php

namespace Wame\Core\Entities\Columns;

trait Slug
{
	/**
	 * @ORM\Column(name="slug", type="string", nullable=true)
	 */
	protected $slug;

	
	/** get ************************************************************/

	public function getSlug()
	{
		return $this->slug;
	}


	/** set ************************************************************/

	public function setSlug($slug)
	{
		$this->slug = $slug;
		
		return $this;
	}
	
}