<?php

namespace Wame\Core\Entities\Columns;

use Nette\Utils\Strings;


trait Slug
{
	/**
	 * @ORM\Column(name="slug", type="string", nullable=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Wame\Core\Doctrine\Generator\SlugGenerator")
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
		$this->slug = Strings::webalize($slug);

		return $this;
	}

}