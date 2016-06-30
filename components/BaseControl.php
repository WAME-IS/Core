<?php 

namespace Wame\Core\Components;

use Nette\Application\UI;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;

class BaseControl extends UI\Control
{
	const DEFAULT_TEMPLATE = 'default.latte';
	
	/** @var ComponentInPositionEntity */
	public $componentInPosition;
	
	/** @var string */
	public $templateFile;
	
	
	/**
	 * Set component in position
	 * 
	 * @param ComponentInPositionEntity $componentInPosition
	 * @return \Wame\AdminModule\Components\BaseControl
	 */
	public function setComponentInPosition($componentInPosition)
	{
		$this->setTemplateFile(null);
		
		if (isset($componentInPosition->component)) {
			$this->componentInPosition = $componentInPosition;

			if ($componentInPosition->component->getParameter('template')) {
				$this->setTemplateFile($componentInPosition->component->getParameter('template'));
			}

			if ($componentInPosition->getParameter('template')) {
				$this->setTemplateFile($componentInPosition->getParameter('template'));
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Set template file
	 * 
	 * @param string $template
	 * @return \Wame\AdminModule\Components\BaseControl
	 */
	public function setTemplateFile($template)
	{
		$this->templateFile = $template;
		
		return $this;
	}


	/**
	 * Get template file path
	 * 
	 * @return \Wame\Core\Components\BaseControl
	 */
	public function getTemplateFile()
	{
		$filePath = dirname($this->getReflection()->getFileName());
		$dir = explode(DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'wame' . DIRECTORY_SEPARATOR, $filePath, 2)[1];
		
		$file = $this->findTemplate($dir);
		
		if (!$file) {
			throw new \Exception(sprintf(_('%s and %s can not be found in %s.'), $this->templateFile, self::DEFAULT_TEMPLATE, $dir));
		}
		
		$this->template->setFile($file);

		return $this;
	}
	
	
	/**
	 * Find the most appropriate template
	 * 
	 * @param string $dir
	 * @return string
	 */
	public function findTemplate($dir)
	{
		$file = null;
		$dirs = [];
		
		$templateFile = $this->templateFile;
		$customTemplate = $this->getCustomTemplate();
		
		if ($templateFile) {
			$dirs[] = APP_PATH . '/' . $dir . '/' . $templateFile;
			if ($customTemplate) { $dirs[] = TEMPLATES_PATH . '/' . $customTemplate . '/' . $dir . '/' . $templateFile; }
			$dirs[] = VENDOR_PATH . '/' . PACKAGIST_NAME . '/' . $dir . '/' . $templateFile;
		}
		
		$dirs[] = APP_PATH . '/' . $dir . '/' . self::DEFAULT_TEMPLATE;
		if ($customTemplate) { $dirs[] = TEMPLATES_PATH . '/' . $customTemplate . '/' . $dir . '/' . self::DEFAULT_TEMPLATE; }
		$dirs[] = VENDOR_PATH . '/' . PACKAGIST_NAME . '/' . $dir . '/' . self::DEFAULT_TEMPLATE;
		
		foreach ($dirs as $dir) {
			if (is_file($dir)) { $file = $dir; break; }
		}
		
		return $file;
	}

	
	/**
	 * Return custom template
	 * 
	 * @return string
	 */
	private function getCustomTemplate()
	{
		if (isset($this->presenter->context->parameters['customTemplate'])) {
			$template = $this->presenter->context->parameters['customTemplate'];
		} else {
			$template = null;
		}

		return $template;
	}
	
	
	/**
	 * Render methods
	 */
	public function componentRender()
	{
		$this->getTemplateFile();

		$this->template->lang = $this->parent->getParameter('lang');
		$this->template->render();
	}
	
	
	/**
	 * Retrun component title
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		return $this->componentInPosition->component->langs[$this->parent->lang]->getTitle();
	}
	
	
	/**
	 * Retrun component description
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->componentInPosition->component->langs[$this->parent->lang]->getDescription();
	}
	
	
	/**
	 * Retrun component name
	 * 
	 * @return string
	 */
	public function getComponentName()
	{
		return $this->componentInPosition->component->getComponentName();
	}
	
	
	/**
	 * Retrun component type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->componentInPosition->component->getType();
	}
	
	
	/**
	 * Retrun component parameters
	 * 
	 * @return array
	 */
	public function getComponentParameters()
	{
		$position = $this->componentInPosition->position->getParameters();
		$component = $this->componentInPosition->component->getParameters();
		
		$parameters = [];
		
		foreach ($position as $key => $value) {
			if ($value != '') {
				$parameters[$key] = $value;
			}
		}
		
		return array_replace($component, $parameters);
	}
	
	
	/**
	 * Retrun component parameter
	 * 
	 * @return string
	 */
	public function getComponentParameter($parameter)
	{
		if (isset($this->getComponentParameters()[$parameter])) {
			return $this->getComponentParameters()[$parameter];
		} else {
			return null;
		}
	}
	
	
	/**
	 * Retrun actual lang
	 * 
	 * @return string
	 */
	public function getLang()
	{
		return $this->parent->lang;
	}
}