<?php

namespace Wame\Core\Forms;

use Nette;
use Nette\Application\UI\Form;

class FormFactory extends Nette\Object
{
	/**
	 * Create Form
	 * 
	 * @return Form
	 */
	public function createForm()
	{
		$form = new Form;
		
		$form->setRenderer(new \Tomaj\Form\Renderer\BootstrapVerticalRenderer);
		
		return $form;
	}

}
