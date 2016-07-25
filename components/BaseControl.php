<?php

namespace Wame\Core\Components;

use Nette\Application\UI;
use Nette\Caching\IStorage;
use Nette\ComponentModel\IContainer;
use Nette\DI\Container;
use Nette\InvalidStateException;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Paremeters\ArrayParameterSource;
use Wame\ComponentModule\Paremeters\IParameterReader;
use Wame\ComponentModule\Paremeters\ParametersCombiner;
use Wame\Core\Cache\TemplatingCache;
use Wame\Core\Status\ControlStatus;
use Wame\Core\Status\ControlStatuses;
use Wame\ComponentModule\Components\PositionControlLoader;

class BaseControl extends UI\Control
{

    const DEFAULT_TEMPLATE = 'default.latte';

    /** @var Container */
    protected $container;

    /** @var ComponentInPositionEntity */
    protected $componentInPosition;

    /** @var ComponentEntity */
    protected $component;

    /** @var string */
    protected $templateFile;

    /** @var ControlStatus */
    protected $status;

    /** @var ParametersCombiner */
    protected $componentParameters;

    /** @var TemplatingCache */
    protected $componentCache;

    public function __construct(Container $container, IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);

        $this->container = $container;
        $container->callInjects($this);

        $this->status = new ControlStatus($this, $container->getByType(ControlStatuses::class));
        $this->componentParameters = new ParametersCombiner();
        $this->componentCache = new TemplatingCache($container->getByType(IStorage::class));
    }

    protected function attached($control)
    {
        parent::attached($control);

        if (!$this->status) {
            throw new InvalidStateException("Control " . get_class($this) . " doesn't call default __construct method.");
        }
        $this->status->callListeners(null, false);
        $this->componentCache->setName($this->getUniqueId());

        $this->container->getByType(PositionControlLoader::class)->load($this);
    }

    /**
     * Set component in position
     * 
     * @param string $type
     * @param ComponentInPositionEntity $componentInPosition
     * @return BaseControl
     */
    public function setComponentInPosition($componentInPosition)
    {
        $this->componentInPosition = $componentInPosition;
        $this->component = $componentInPosition->component;

        //add paramter sources
        $this->componentParameters->add(
            new ArrayParameterSource($componentInPosition->getParameters()), 'componentInPosition', 30);
        $this->componentParameters->add(
            new ArrayParameterSource($this->component->getParameters()), 'component', 20);

        //update template if specified in parameters
        $template = $this->getComponentParameter("template");
        if ($template) {
            $this->setTemplateFile($template);
        }

        return $this;
    }

    /**
     * Set template file
     * 
     * @param string $template
     * @return BaseControl
     */
    public function setTemplateFile($template)
    {
        $this->templateFile = $template;

        return $this;
    }

    /**
     * Get template file path
     * 
     * @return BaseControl
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
            if ($customTemplate) {
                $dirs[] = TEMPLATES_PATH . '/' . $customTemplate . '/' . $dir . '/' . $templateFile;
            }
            $dirs[] = VENDOR_PATH . '/' . PACKAGIST_NAME . '/' . $dir . '/' . $templateFile;
        }

        $dirs[] = APP_PATH . '/' . $dir . '/' . self::DEFAULT_TEMPLATE;
        if ($customTemplate) {
            $dirs[] = TEMPLATES_PATH . '/' . $customTemplate . '/' . $dir . '/' . self::DEFAULT_TEMPLATE;
        }
        $dirs[] = VENDOR_PATH . '/' . PACKAGIST_NAME . '/' . $dir . '/' . self::DEFAULT_TEMPLATE;

        foreach ($dirs as $dir) {
            if (is_file($dir)) {
                $file = $dir;
                break;
            }
        }

        return $file;
    }

    /**
     * Return custom temp
     * late
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

    public function willRender($method, $params = null)
    {
        $this->componentCache->cachedOutput(function() use ($method, $params) {
            $reflection = new \Nette\Reflection\Method($this, $method);

            $renderParamsSource = null;
            if ($params && $reflection->getParameters()) {

                $i = 0;
                $namedParams = [];
                foreach ($reflection->getParameters() as $paramRefl) {
                    if ($i >= count($params)) {
                        break;
                    }
                    $namedParams[$paramRefl->getName()] = $params[$i];
                    $i++;
                }

                $renderParamsSource = new ArrayParameterSource($namedParams);
                $this->componentParameters->add($renderParamsSource);
            }

            $loadedParams = [];
            foreach ($reflection->getParameters() as $paramRefl) {
                $loadedParams[$paramRefl->getName()] = $this->getComponentParameter($paramRefl->getName());
            }

            //Pre-render
            call_user_func_array([$this, $method], $loadedParams);
            //Post-render

            if ($renderParamsSource) {
                $this->componentParameters->remove($renderParamsSource);
            }
        }, $params);
    }

    /**
     * Render methods
     */
    public function componentRender()
    {
        $this->getTemplateFile();

        if (!isset($this->template->lang)) {
            $this->template->lang = $this->parent->getParameter('lang');
        }

        $this->template->render();
    }

    /**
     * Retrun component title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->component->getTitle();
    }

    /**
     * Retrun component description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->component->getDescription();
    }

    /**
     * Retrun component type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->component->getType();
    }

    /**
     * Retrun component parameters object
     * 
     * @return ParametersCombiner
     */
    public function getComponentParameters()
    {
        return $this->componentParameters;
    }

    /**
     * Get component parameter
     * 
     * @param string $parameter Name of parameter
     * @param IParameterReader|array $parameterReader
     * @return string
     */
    public function getComponentParameter($parameter, $parameterReader = null)
    {
        return $this->componentParameters->getParameter($parameter, $parameterReader);
    }

    /**
     * Get component cache settings
     * 
     * @return ComponentCache
     */
    function getComponentCache()
    {
        return $this->componentCache;
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
