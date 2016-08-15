<?php

namespace App\Core\Presenters;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Replicator\Container;
use Nette\Application\UI\Control;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Nette\Templating\ITemplate;
use Wame\ComponentModule\Components\PositionControlLoader;
use Wame\Core\Components\BaseControl;
use Wame\Core\Status\ControlStatus;
use Wame\Core\Status\ControlStatuses;
use Wame\DynamicObject\Components\IFormControlFactory;
use Wame\HeadControl\Components\IHeadControlFactory;
use Wame\HeadControl\Registers\MetaTypeRegister;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;
use WebLoader\Nette\LoaderFactory;

abstract class BasePresenter extends Presenter
{

    /** h4kuna Gettext latte translator trait */
    use \h4kuna\Gettext\InjectTranslator;

    /** FormGroup getter trait */
    use \Wame\DynamicObject\Forms\FormGroup;

    /** @var LoaderFactory @inject */
    public $webLoader;

    /** @var EntityManager @inject */
    public $entityManager;

    /** @persistent */
    public $id;

    /** @var MetaTypeRegister */
    public $metaTypeRegister;

    /** @var IHeadControlFactory */
    public $IHeadControlFactory;

    /** @var IFormControlFactory @inject */
    public $IFormControlFactory;

    /** @var PositionControlLoader @inject */
    public $positionControlLoader;

    /** @var ControlStatus */
    public $status;

    /**
     * Event
     * 
     * @var callable[] 
     */
    public $onBeforeRender = [];

    /**
     * Event
     * 
     * @var callable[] 
     */
    public $onAfterRender = [];

    public function injectStatus(ControlStatuses $controlStatuses)
    {
        $this->status = new ControlStatus($this, $controlStatuses);
    }

    public function injectHeadControl(MetaTypeRegister $metaTypeRegister, IHeadControlFactory $IHeadControlFactory)
    {
        $this->metaTypeRegister = $metaTypeRegister;
        $this->IHeadControlFactory = $IHeadControlFactory;
    }

    protected function startup()
    {
        parent::startup();
        $this->positionControlLoader->load($this);
        Container::register();
    }

    /** @return CssLoader */
    protected function createComponentCss()
    {
        return $this->webLoader->createCssLoader('default');
    }

    /** @return JavaScriptLoader */
    protected function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('default');
    }

    // TODO: presunut do global component loadera
    public function createComponentHeadControl()
    {
        return $this->IHeadControlFactory->create();
    }

    /**
     * Form control
     * 
     * @return IFormControlFactory
     */
    protected function createComponentForm()
    {
        return new Multiplier(function ($formName) {
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
        $presenter = $this->getTemplatePresenter();
        $module = $this->getModule();

        $dir = $this->getTemplatesFolder();

        $dirs = [];

        $dirs[] = APP_PATH . '/' . $module . $way . '/presenters/templates';

        if ($this->customTemplate) {
            $dirs[] = TEMPLATES_PATH . '/' . $this->customTemplate . '/' . $module . $way . '/presenters/templates';
        }

        $dirs[] = $dir . '/templates';

        $paths = [];

        if ($this->isAjax()) {
            foreach ($dirs as $dir) {
                array_push($paths, "$dir/$presenter/ajax-$this->view.latte");
            }
        }

        foreach ($dirs as $dir) {
            array_push($paths, "$dir/$presenter/$this->view.latte");
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
        $presenter = $this->getTemplatePresenter();
        $module = $this->getModule();
        $layout = $this->layout ? $this->layout : 'layout';

        $dir = $this->getTemplatesFolder();

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

        $name = $this->getName();

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

    protected function getTemplatePresenter()
    {
        return substr($this->getName(), strrpos(':' . $this->getName(), ':'));
    }

    protected function getTemplatesFolder()
    {
        $dir = dirname($this->getReflection()->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        return $dir;
    }

    /**
     * Create template
     * Append vars to template
     * 
     * @return ITemplate
     */
    public function createTemplate()
    {
        $template = parent::createTemplate();

        $template->lang = $this->lang;
        $template->id = $this->id;
        $template->siteTitle = null;

        return $template;
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->onBeforeRender();
        $this->callBeforeRenders($this);
    }

    private function callBeforeRenders(Control $control)
    {
        //TODO check if it can be removed
        foreach ($control->getComponents() as $subControl) {
            if ($subControl instanceof BaseControl) {
                $subControl->beforeRender();
            }
            if ($subControl instanceof Control) {
                $this->callBeforeRenders($subControl);
            }
        }
    }

    protected function afterRender()
    {
        parent::afterRender();
        $this->onAfterRender();
    }

    /**
     * End of presenter cycle
     * 
     * @param IResponse $response
     */
    protected function shutdown($response)
    {
        parent::shutdown($response);

        $this->entityManager->flush();
    }

    /**
     * Get presenter status
     * @return ControlStatus
     */
    public function getStatus()
    {
        return $this->status;
    }
}
