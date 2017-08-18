<?php

namespace Wame\Core\Entities\Columns;


trait Identifier
{
	/**
     * @ORM\Column(name="id", type="integer", length=11, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

	
	/** get ***********************************************************************************************************/

	public function getId()
	{
		return $this->id;
	}


	/** set ***********************************************************************************************************/

	public function setId($id)
	{
		$this->id = $id;
		
		return $this;
	}
	
}
