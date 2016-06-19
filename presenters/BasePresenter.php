<?php

namespace App\Core\Presenters;

use Nette;
use Wame\HeadControl\HeadControl;
use Wame\PositionModule\Components\IPositionControlFactory;

abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** h4kuna Gettext latte translator trait */
	use \h4kuna\Gettext\InjectTranslator;

	/** @var \WebLoader\Nette\LoaderFactory @inject */
	public $webLoader;

	/** @var \Kdyby\Doctrine\EntityManager @inject */
	public $entityManager;

	/** @persistent */
	public $id;

	/** @var HeadControl @inject */
	public $headControl;

	/** @var IPositionControlFactory @inject */
	public $IPositionControlFactory;

	/** @return CssLoader */
	protected function createComponentCss() {
		return $this->webLoader->createCssLoader('frontend');
	}

	/** @return JavaScriptLoader */
	protected function createComponentJs() {
		return $this->webLoader->createJavaScriptLoader('frontend');
	}

	// TODO: presunut do global component loadera
	public function createComponentHeadControl() {
		return $this->headControl;
	}

	/**
	 * Position control
	 * 
	 * @return IPositionControlFactory
	 */
	protected function createComponentPositionControl() {
		$control = $this->IPositionControlFactory->create();

		return $control;
	}

	/**
	 * Return module name
	 * 
	 * @param string $name
	 * @return string
	 */
	public function getModule($name = null) {
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
	public function getCustomTemplate() {
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
	public function formatTemplateFiles() {
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$module = $this->getModule();

		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);

		$dirs = [];

		$dirs[] = APP_PATH . '/' . $module . '/presenters/templates';

		if ($this->customTemplate) {
			$dirs[] = TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $module . '/presenters/templates';
		}

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
	public function formatLayoutTemplateFiles() {
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$module = $this->getModule();
		$layout = $this->layout ? $this->layout : 'layout';

		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);

		$dirs = [];

		$dirs[] = APP_PATH . '/' . $module . '/presenters/templates';

		if ($this->customTemplate) {
			$dirs[] = TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $module . '/presenters/templates';
		}

		$dirs[] = $dir . '/templates';

		$list = [];

		foreach ($dirs as $dir) {
			array_push($list, "$dir/$presenter/@$layout.latte", "$dir/$presenter.@$layout.latte");

			do {
				$list[] = "$dir/@$layout.latte";
				$dir = dirname($dir);
			} while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
		}

		array_push($list, APP_PATH . '/Core/presenters/templates/@layout.latte');

		if ($this->customTemplate) {
			array_push($list, TEMPLATES_PATH . '/' . $this->customTemplate . '/Core/presenters/templates/@layout.latte');
		}

		array_push($list, VENDOR_PATH . '/' . PACKAGIST_NAME . '/Core/presenters/templates/@layout.latte');

		return $list;
	}

	/**
	 * Create template
	 * 
	 * @return Nette\Application\UI\ITemplate
	 */
	public function createTemplate() {
		$template = parent::createTemplate();

		$template->lang = $this->lang;
		$template->id = $this->id;

		return $template;
	}

	protected function shutdown($response) {
		parent::shutdown($response);

		$this->entityManager->flush();
	}

}
