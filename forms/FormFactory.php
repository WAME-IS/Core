<?php

namespace Wame\Core\Forms;

use Nette\Utils\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Container;


class FormFactory extends Control
{
	/** @var int */
	public $id;
	
	/** @var array */
	public $formContainers = [];
	
	/** @var array */
	public $removeFormContainers = [];
	
	/** @var string */
	public $actionForm;
	

	/**
	 * Create Form
	 * 
	 * @return Form
	 */
	public function createForm()
	{
		$form = new Form;
        
        $form->setParent();
		
		$form->setRenderer(new \Tomaj\Form\Renderer\BootstrapVerticalRenderer);

		$this->getActionForm($form);
		
		$this->attachFormContainers($form);
		
		return $form;
	}
	
	/**
	 * Attach for callbacks
	 * 
	 * @param type $object
	 */
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
		$this->formContainers[$name] = [
			'name' => $name,
			'priority' => $priority,
			'service' => $service
		];
		
		return $this;
	}
	
	
	/**
	 * Remove form container
	 * 
	 * @param string $name
	 * @return \Wame\Core\Forms\FormFactory
	 */
	public function removeFormContainer($name)
	{
		$this->removeFormContainers[$name] = $name;
		
		return $this;
	}
	
	
	/**
	 * Remove form containers from array
	 */
	private function removeFormContainers()
	{
		foreach ($this->removeFormContainers as $name)
		{
			if (array_key_exists($name, $this->formContainers)) {
				unset($this->formContainers[$name]);
			}
		}
	}
	
	
	/**
	 * Add form containers
	 * 
	 * @param array $containers
	 * @return array
	 */
	public function addFormContainers($containers)
	{
		$this->formContainers = array_merge($containers, $this->formContainers);
		
		return $this;
	}
	
	
	/**
	 * Return form containers
	 * 
	 * @return array
	 */
	public function getFormContainers()
	{
		return $this->formContainers;
	}
	
	
	/**
	 * Get form container
	 * 
	 * @param string $name
	 * @return Container
	 */
	public function getFormContainer($name)
	{
		return $this->getFormContainers()[$name];
	}
	
	
	/**
	 * Sort form containers by priority
	 * 
	 * @return array
	 */
	public function sortFormContainers()
	{
		$this->removeFormContainers();

		$formContainers = [];
		
		foreach ($this->formContainers as $container)
		{
			$formContainers[$container['priority']][] = [
				'name' => $container['name'],
				'service' => $container['service']
			];
		}
		
		// Sort by priority
		krsort($formContainers);
		
		return $formContainers;
	}
	

	/**
	 * Attach form containers
	 * 
	 * @param Form $form
	 * @return Form
	 */
	public function attachFormContainers($form)
	{
		foreach ($this->sortFormContainers() as $containers) {
			foreach ($containers as $container) {
				$form->addComponent($container['service'], $container['name']);
			}
		}
		
		return $form;
	}
	
	
	/**
	 * Set action form
	 * 
	 * @param string $action
	 */
	public function setActionForm($action)
	{
		$this->actionForm = $action;
		
		return $this;
	}
	
	
	/**
	 * Get action form
	 * 
	 * @param Form $form
	 * @return Form
	 * @throws \Exception
	 */
	private function getActionForm(Form $form)
	{
		if ($this->actionForm) {
			$form->setAction($this->actionForm);
		}
		
		return $form;
	}
	
	
	/**
	 * Set database identifier
	 * 
	 * @param int $id
	 * @return \Wame\Core\Forms\FormFactory
	 */
	public function setId($id)
	{
		$this->id = $id;
		
		return $this;
	}
	
	
	/**
	 * Set default values in form containers
	 */
	public function setDefaultValues()
	{
		foreach ($this->formContainers as $container) {
			if (method_exists($container['service'], 'setDefaultValues')) {
				$container['service']->setDefaultValues($this);
			}
		}
	}

}
