<?php

namespace Wame\Core\Forms;

use Wame\DynamicObject\Forms\BaseFormContainer;
use Wame\Core\Registers\StatusTypeRegister;

interface IStatusTypeFormContainerFactory
{
	/** @return StatusTypeFormContainer */
	public function create();
}


class StatusTypeFormContainer extends BaseFormContainer
{
	/** @var StatusTypeRegister */
	private $statusTypeRegister;


	public function __construct(StatusTypeRegister $statusTypeRegister) 
	{
		parent::__construct();
		
		$this->statusTypeRegister = $statusTypeRegister;
	}


    public function configure() 
	{
		$form = $this->getForm();
        
        $types = $this->statusTypeRegister->getAll();
        
        $pairs = [];
        
        foreach($types as $type) {
            /* @var $type \Wame\Core\Registers\Types\IStatusType */
            $pairs[$type->getAlias()] = $type->getTitle();
        }

        ksort($pairs);
		
		$form->addSelect('statusType', _('Status type'), $pairs)
                ->setPrompt('- ' . _('Select status type') . ' -');
    }


	public function setDefaultValues($object)
	{
		$form = $this->getForm();
		$form['statusType']->setDefaultValue($object->componentEntity->getParameter('statusType'));
	}

}