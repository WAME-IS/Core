<?php

namespace Wame\Core\Entities\Columns;

trait Slug
{
	/**
	 * @ORM\Column(name="slug", type="string", nullable=true)
	 */
	protected $slug;

	
	public function getSlug()
	{
		return $this->slug;
	}


	public function setSlug($slug)
	{
		$this->slug = $slug;
		
		return $this;
	}
	
}