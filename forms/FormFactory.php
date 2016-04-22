<?php

namespace Wame\Core\Forms;

use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class FormFactory extends Control
{
	/** @var array */
	public $formContainers = [];
	
	/**
	 * Create Form
	 * 
	 * @return Form
	 */
	public function createForm()
	{
		$form = new Form;
		
		$form->setRenderer(new \Tomaj\Form\Renderer\BootstrapVerticalRenderer);
		
		$this->attachFormContainers($form);
		
		return $form;
	}
	
	protected function attached($object) 
    {
        parent::attached($object);
    }
	
	/**
	 * Add container to form
	 * 
	 * @param object $service
	 * @param string $name
	 * @param int $priority
	 * @return \Wame\Core\Forms\FormFactory
	 */
	public function addFormContainer($service, $name = null, $priority = 0)
	{
		$this->formContainers[$priority][] = [
			'name' => $name,
			'service' => $service
		];
		
		return $this;
	}
	
	/**
	 * Attach form containers
	 * 
	 * @param Form $form
	 * @return Form
	 */
	public function attachFormContainers($form)
	{	
		// Sort by priority
		krsort($this->formContainers);
		
		foreach ($this->formContainers as $containers)
		{
			foreach ($containers as $container) {
				$service = new $container['service'];
				$form->addComponent($service, $container['name']);
			}
		}
		
		return $form;
	}

}
