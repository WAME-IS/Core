<?php

namespace Wame\Core\Forms;

use Nette\Utils\DateTime;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class FormFactory extends Control
{
	/** @var int */
	public $id;
	
	/** @var array */
	public $formContainers = [];
	
	/** @var array */
	public $removeFormContainers = [];
	

	/**
	 * Create Form
	 * 
	 * @return Form
	 */
	public function createForm()
	{
		$form = new Form;
		
		$form->setRenderer(new \App\Core\Forms\CustomRenderer);
		
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
	 * Sort form containers by priority
	 * 
	 * @return array
	 */
	private function sortFormContainers()
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
	
	
	/**
	 * Format string date to DateTime for Doctrine entity
	 * 
	 * @param DateTime $date
	 * @param string $format
	 * @return DateTime
	 */
	public function formatDate($date, $format = 'Y-m-d H:i:s')
	{
		if ($date == 'now') {
			return new DateTime('now');
		} else {
			return new DateTime(date($format, strtotime($date)));
		}
	}

}
