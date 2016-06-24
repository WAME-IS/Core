<?php

namespace App\Core\Presenters;

use Nette;
use Wame\HeadControl\HeadControl;
use Wame\DynamicObject\Components\IFormControlFactory;
use Wame\PositionModule\Components\IPositionControlFactory;
use Wame\UserModule\Repositories\UserRepository;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
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

	/** @var IFormControlFactory @inject */
	public $IFormControlFactory;

	/** @var IPositionControlFactory @inject */
	public $IPositionControlFactory;
	
	/** @var UserRepository @inject */
	public $userRepository;
	
	/** @var UserEntity */
	public $yourUserEntity;
	
	
	protected function startup() 
	{
		parent::startup();
		
		$this->yourUserEntity = $this->userRepository->get(['id' => $this->user->id]);
	}
	
	
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


	// TODO: presunut do global component loadera
	public function createComponentHeadControl()
	{
		return $this->headControl;
	}

	
	/**
	 * Position control
	 * 
	 * @return IPositionControlFactory
	 */
	protected function createComponentPositionControl()
	{
		$control = $this->IPositionControlFactory->create();
	
		return $control;
	}

	
	/**
	 * Form control
	 * 
	 * @return IFormControlFactory
	 */
	protected function createComponentForm()
	{
		return new \Nette\Application\UI\Multiplier(function ($formName) {
			return $this->IFormControlFactory->create($formName);
		});
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
	public function formatTemplateFiles($way = '')
	{
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$module = $this->getModule();

		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);

		$dirs = [];

		$dirs[] = APP_PATH . '/' . $module . $way . '/presenters/templates';

		if ($this->customTemplate) {
			$dirs[] = TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $module . $way . '/presenters/templates';
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
	public function formatLayoutTemplateFiles($modulePath = 'Core', $way = '')
	{
		$name = $this->getName();
		$presenter = substr($name, strrpos(':' . $name, ':'));
		$module = $this->getModule();
		$layout = $this->layout ? $this->layout : 'layout';

		$dir = dirname($this->getReflection()->getFileName());
		$dir = is_dir("$dir/templates") ? $dir : dirname($dir);

		$dirs = [];

		$dirs[] = APP_PATH . '/' . $module . $way . '/presenters/templates';

		if ($this->customTemplate) {
			$dirs[] = TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $module . $way . '/presenters/templates';
		}

		$dirs[] = $dir . '/templates';

		$list = [];
		
		if ($this->isAjax()) {
			$list[] = __DIR__ . '/templates/@modalLayout.latte';
		}

		foreach ($dirs as $dir) {
			array_push($list, "$dir/$presenter/@$layout.latte", "$dir/$presenter.@$layout.latte");

			do {
				$list[] = "$dir/@$layout.latte";
				$dir = dirname($dir);
			} while ($dir && ($name = substr($name, 0, strrpos($name, ':'))));
		}

		array_push($list, APP_PATH . '/' . $modulePath . '/presenters/templates/@layout.latte');

		if ($this->customTemplate) {
			array_push($list, TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $modulePath . '/presenters/templates/@layout.latte');
		}

		array_push($list, VENDOR_PATH . '/' . PACKAGIST_NAME . '/' . $modulePath . '/presenters/templates/@layout.latte');

		return $list;
	}
	
	
	/**
     * Create template
     * 
     * @return Nette\Application\UI\ITemplate
     */
    public function createTemplate()
    {
        $template = parent::createTemplate();
        
        $template->lang = $this->lang;
        $template->id = $this->id;
        
        return $template;
    }
	

	/**
	 * Format DateTime to string
	 * 
	 * @param \DateTime $date
	 * @param string $format
	 * @return string
	 */
	public function formatDate($date, $format = 'Y-m-d H:i:s')
	{
		return $date->format($format);
	}
	
	
	protected function shutdown($response) 
	{
		parent::shutdown($response);

		$this->entityManager->flush();
	}

}
