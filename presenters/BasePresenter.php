<?php

namespace App\Core\Presenters;

use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var \WebLoader\Nette\LoaderFactory @inject */
	public $webLoader;
	
	/** @var \Kdyby\Doctrine\EntityManager @inject */
	public $entityManager;

	/** @return CssLoader */
	protected function createComponentCss()
	{
		return $this->webLoader->createCssLoader('frontend');
	}

	/** @return JavaScriptLoader */
	protected function createComponentJs()
	{
		return $this->webLoader->createJavaScriptLoader('frontend');
	}

	/**
	* Return module name
	* 
	* @param string $name
	* @return string
	*/
	public function getModule($name = null)
	{
		if (is_null($name)) {
			$name = $this->name;
		}

		$module = preg_replace("#:?[a-zA-Z_0-9]+$#", "", $name);

		return $module . 'Module';
	}

	/**
	* Return custom template
	* 
	* @return string
	*/
	public function getCustomTemplate()
	{
		if (isset($this->context->parameters['customTemplate'])) {
			$template = $this->context->parameters['customTemplate'];
		} else {
			$template = null;
		}

		return $template;
	}

	/**
	* Return template file
	* use current Module, Presenter
	* resolve customTemplates
	* 
	* @return array
	*/
	public function formatTemplateFiles()
	{
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$module = $this->getModule();

		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);

		$dirs = [];

		if ($this->customTemplate) {
			$dirs[] = TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $module . '/presenters/templates';
		}

		$dirs[] = APP_PATH . '/' . $module . '/presenters/templates';
		$dirs[] = $dir . '/templates';

		$paths = [];

		foreach ($dirs as $dir) {
			array_push($paths, "$dir/$presenter/$this->view.latte", "$dir/$presenter.$this->view.latte");
		}

		return $paths;
	}

	/**
	* Return layout file
	* use current Module, Presenter
	* resolve customTemplates
	* 
	* @return array
	*/
	public function formatLayoutTemplateFiles()
	{
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$module = $this->getModule();
		$layout = $this->layout ? $this->layout : 'layout';

		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);

		$dirs = [];

		if ($this->customTemplate) {
			$dirs[] = TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $module . '/presenters/templates';
		}

		$dirs[] = APP_PATH . '/' . $module . '/presenters/templates';
		$dirs[] = $dir . '/templates';

		$list = [];

		foreach ($dirs as $dir) {
			array_push($list, "$dir/$presenter/@$layout.latte", "$dir/$presenter.@$layout.latte");

			do {
				$list[] = "$dir/@$layout.latte";
				$dir = dirname($dir);
			} while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
		}

		if ($this->customTemplate) {
			array_push($list, TEMPLATES_PATH . '/' . $this->customTemplate . '/Core/presenters/templates/@layout.latte');
		}

		array_push($list, APP_PATH . '/Core/presenters/templates/@layout.latte');
		array_push($list, VENDOR_PATH . '/' . PACKAGIST_NAME . '/Core/presenters/templates/@layout.latte');

		return $list;
	}

}
