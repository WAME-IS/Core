<?php

namespace App\Core\Presenters;

use h4kuna\Gettext\InjectTranslator;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Replicator\Container;
use Nette\Application\UI\Control;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Nette\Application\UI\ITemplate;
use Nette\Utils\Strings;
use Wame\ComponentModule\Components\PositionControlLoader;
use Wame\Core\Components\BaseControl;
use Wame\Core\Event\PresenterStageChangeEvent;
use Wame\Core\Status\ControlStatus;
use Wame\Core\Status\ControlStatuses;
use Wame\DynamicObject\Components\IFormControlFactory;
use Wame\DynamicObject\Forms\FormGroup;
use Wame\HeadControl\Registers\MetaTypeRegister;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;
use WebLoader\Nette\LoaderFactory;
use Wame\LanguageModule\Gettext\Dictionary;

abstract class BasePresenter extends Presenter
{
    /** h4kuna Gettext latte translator trait */
    use InjectTranslator;

    /** FormGroup getter trait */
    use FormGroup;


    /** @var LoaderFactory @inject */
    public $webLoader;

    /** @var EntityManager @inject */
    public $entityManager;

    /** @persistent */
    public $id;

    /** @var MetaTypeRegister */
    public $metaTypeRegister;

    /** @var IFormControlFactory @inject */
    public $IFormControlFactory;

    /** @var PositionControlLoader @inject */
    public $positionControlLoader;

    /** @var Dictionary @inject */
    public $dictionary;

    /** @var ControlStatus */
    public $status;

    /**
     * Event called whenever processing stage of presenter changes. Stages are: startup, action, signal, render, terminate
     * Callback should have one argument of PresenterStageChangeEvent type
     *
     * @var callable[]
     */
    public $onStageChange = [];


    /** injects ***************************************************************/

    /**
     * @param ControlStatuses $controlStatuses
     */
    public function injectStatus(ControlStatuses $controlStatuses)
    {
        $this->status = new ControlStatus($this, $controlStatuses);
    }

    /**
     * @param MetaTypeRegister $metaTypeRegister
     */
    public function injectHeadControl(MetaTypeRegister $metaTypeRegister)
    {
        $this->metaTypeRegister = $metaTypeRegister;
    }


    /** lifecycle *************************************************************/

    /**
     * Handle redraw component
     */
    public function handleRedrawControl()
    {
        $name = $this->getParameter('name');

        if($name) {
            $this[$name]->redrawControl();
        }
    }

    protected function startup()
    {
        parent::startup();

        $this->onStageChange(new PresenterStageChangeEvent($this, 'startup'));
        $this->dictionary->setDomain($this);
        $this->positionControlLoader->load($this);

        Container::register();
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
     * Before renderer
     */
    protected function beforeRender()
    {
        parent::beforeRender();

        $this->dictionary->setDomain($this);
        $this->onStageChange(new PresenterStageChangeEvent($this, 'render'));
        $this->callBeforeRenders($this);
    }

    /**
     * After renderer
     */
    protected function afterRender()
    {
        parent::afterRender();
        $this->onStageChange(new PresenterStageChangeEvent($this, 'terminate'));
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

        if ($this->getCustomTemplate()) {
            $dirs[] = TEMPLATES_PATH . '/' . $this->getCustomTemplate() . '/' . $module . $way . '/presenters/templates';
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

        if ($this->getCustomTemplate()) {
            $dirs[] = TEMPLATES_PATH . '/' . $this->getCustomTemplate() . '/' . $module . $way . '/presenters/templates';
        }

        $dirs[] = $dir . '/templates';

        $list = [];

        if ($this->isAjax() && $this->getHttpRequest()->getHeader("X-Modal") == true) {
            $list[] = VENDOR_PATH . '/wame/' . $modulePath . '/presenters/templates/@modalLayout.latte';
        } elseif ($this->isAjax() && $this->getHttpRequest()->getHeader("X-Component") == true) {
            $list[] = VENDOR_PATH . '/wame/Core/presenters/templates/@emptyLayout.latte';
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

        if ($this->getCustomTemplate()) {
            array_push($list, TEMPLATES_PATH . '/' . $this->getCustomTemplate() . '/' . $modulePath . '/presenters/templates/@layout.latte');
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
        $template->subTitle = null;

        return $template;
    }

    /**
     * Get presenter status
     * @return ControlStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    protected function tryCall($method, array $params)
    {
        $callEvent = Strings::startsWith($method, 'action');
        if ($callEvent) {
            $this->onStageChange(new PresenterStageChangeEvent($this, 'action'));
        }

        $tryCallResult = parent::tryCall($method, $params);

        if ($callEvent) {
            $this->onStageChange(new PresenterStageChangeEvent($this, 'signal'));
        }

        return $tryCallResult;
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

    public function getEntity()
    {

    }


    /** components ************************************************************/

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

    /**
     * Form control
     *
     * @return Multiplier
     */
    protected function createComponentForm()
    {
        return new Multiplier(function ($formName) {
            return $this->IFormControlFactory->create($formName);
        });
    }

}
