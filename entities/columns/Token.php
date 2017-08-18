<?php

namespace Wame\Core\Entities\Columns;


trait Token
{
    /**
     * @ORM\Column(name="token", type="string", length=64, nullable=true)
     */
    protected $token = null;

	
	/** get ***********************************************************************************************************/

	public function getToken()
	{
		return $this->token;
	}


	/** set ***********************************************************************************************************/

	public function setToken($token)
	{
		$this->token = $token;
		
		return $this;
	}
	
}
