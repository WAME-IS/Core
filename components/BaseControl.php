<?php 

namespace App\Core\Components;

use Nette\Application\UI;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;

class BaseControl extends UI\Control
{
	const DEFAULT_TEMPLATE = 'default.latte';
	
  	/** @var string */
	public $componentName;
	
	/** @var ComponentInPositionEntity */
	public $componentInPosition;
	
	/** @var string */
	public $templateFile;
	
	
	/**
	 * Set component name
	 * 
	 * @param string $componentName
	 * @return \App\AdminModule\Components\BaseControl
	 */
	public function setComponentName($componentName)
	{
		$this->componentName = $componentName;
		
		return $this;
	}
	
	
	/**
	 * Set component in position
	 * 
	 * @param ComponentInPositionEntity $componentInPosition
	 * @return \App\AdminModule\Components\BaseControl
	 */
	public function setComponentInPosition($componentInPosition)
	{
		$this->componentInPosition = $componentInPosition;
		
		if ($componentInPosition->component->getParameter('template')) {
			$this->setTemplateFile($componentInPosition->component->getParameter('template'));
		}
		
		if ($componentInPosition->getParameter('template')) {
			$this->setTemplateFile($componentInPosition->getParameter('template'));
		}
		
		return $this;
	}
	
	
	/**
	 * Set template file
	 * 
	 * @param string $template
	 * @return \App\AdminModule\Components\BaseControl
	 */
	public function setTemplateFile($template)
	{
		$this->templateFile = $template;
		
		return $this;
	}


	/**
	 * Get template file path
	 * 
	 * @return \App\Core\Components\BaseControl
	 */
	public function getTemplateFile()
	{
		$customTemplate = $this->getCustomTemplate();
		$filePath = dirname($this->getReflection()->getFileName());
		$dir = explode('/vendor/wame/', $filePath)[1];
		$templateFile = $this->templateFile;

		$dirs = [];
		
		if ($templateFile) {
			if ($customTemplate) { $dirs[] = TEMPLATES_PATH . '/' . $customTemplate . '/' . $dir . '/' . $templateFile; }
			$dirs[] = APP_PATH . '/' . $dir . '/' . $templateFile;
			$dirs[] = VENDOR_PATH . '/' . PACKAGIST_NAME . '/' . $dir . '/' . $templateFile;
		}
		
		if ($customTemplate) { $dirs[] = TEMPLATES_PATH . '/' . $customTemplate . '/' . $dir . '/' . self::DEFAULT_TEMPLATE; }
		$dirs[] = APP_PATH . '/' . $dir . '/' . self::DEFAULT_TEMPLATE;
		$dirs[] = VENDOR_PATH . '/' . PACKAGIST_NAME . '/' . $dir . '/' . self::DEFAULT_TEMPLATE;
		
		foreach ($dirs as $dir) {
			if (is_file($dir)) { $file = $dir; break; }
		}
		
		$this->template->setFile($file);

		return $this;
	}

	
	/**
	 * Return custom template
	 * 
	 * @return string
	 */
	public function getCustomTemplate()
	{
		if (isset($this->presenter->context->parameters['customTemplate'])) {
			$template = $this->presenter->context->parameters['customTemplate'];
		} else {
			$template = null;
		}

		return $template;
	}
	
}