<?php

namespace Wame\Core\Components;

use Nette\Application\UI\Control;
use Nette\ComponentModel\IContainer;
use Nette\DI\Container;
use Nette\InvalidStateException;
use Nette\Reflection\Method;
use Nette\Security\User;
use Nette\Utils\Strings;
use Wame\ComponentModule\Components\PositionControlLoader;
use Wame\ComponentModule\Entities\ComponentEntity;
use Wame\ComponentModule\Entities\ComponentInPositionEntity;
use Wame\ComponentModule\Helpers\Helpers;
use Wame\ComponentModule\Paremeters\ArrayParameterSource;
use Wame\ComponentModule\Paremeters\IParameterReader;
use Wame\ComponentModule\Paremeters\ParametersCombiner;
use Wame\Core\Cache\TemplatingCache;
use Wame\Core\Cache\TemplatingCacheFactory;
use Wame\Core\Status\ControlStatus;
use Wame\Core\Status\ControlStatuses;
use Wame\Utils\Strings as Strings2;

abstract class BaseControl extends Control
{
    const
        DEFAULT_TEMPLATE = 'default.latte',
        PARAM_CONTAINER = 'container',
        CONTAINER_DEFAULT = [
            'tag' => 'div'
        ],
        COMPONENT_ID_CLASS = 'cnt-%s';

    
    /**
     * Event called before rendeing of control
     * 
     * @var callable[] 
     */
    public $onBeforeRender = [];

    /**
     * Event called after rendeing of control
     * 
     * @var callable[] 
     */
    public $onAfterRender = [];
    
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

    /** @var boolean */
    protected $hasContainer = true;

    /** @var User */
    protected $user;
    
    
    
    use \Wame\ComponentModule\Traits\TComponentStatusType;

    
    public function __construct(Container $container, IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);

        $this->container = $container;
        $container->callInjects($this);

        $this->status = new ControlStatus($this, $container->getByType(ControlStatuses::class));
        $this->componentParameters = new ParametersCombiner();
        $this->componentCache = $container->getByType(TemplatingCacheFactory::class)->create();
        
        $type = Strings2::getClassName(get_class($this));
        $this->componentParameters->add(
            new ArrayParameterSource(['container' => ['class' => sprintf(self::COMPONENT_ID_CLASS, $type)]]), 'componentDefaultClass', ['priority' => 1]);
        
        $this->bindContainers();
    }
    
    
    /**
     * @internal
     */
    public function injectUser(User $user)
    {
        $this->user = $user;
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
            new ArrayParameterSource($componentInPosition->getParameters()), 'componentInPosition', ['priority' => 30]);
        $this->componentParameters->add(
            new ArrayParameterSource($this->component->getParameters()), 'component', ['priority' => 20]);

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
        if (!Strings::contains($template, ".")) {
            $template .= '.latte';
        }

        $this->templateFile = $template;

        return $this;
    }

    /**
     * Fills template with selected template file path
     */
    public function getTemplateFile()
    {
        if ($this->template->getFile()) {
            return;
        }

        $filePath = dirname($this->getReflection()->getFileName());
        $dir = explode(DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'wame' . DIRECTORY_SEPARATOR, $filePath, 2)[1];

        $file = $this->findTemplate($dir);

        if (!$file) {
            throw new InvalidStateException(sprintf(_('%s and %s can not be found in %s.'), $this->templateFile, self::DEFAULT_TEMPLATE, $dir));
        }

        $this->template->setFile($file);

        return;
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

    /**
     * Function called before render
     */
    public function beforeRender()
    {
        
    }

    /**
     * @internal
     */
    public function willRender($method, $params = null)
    {
        $this->componentCache->cachedOutput($this, function() use ($method, $params) {
            $reflection = new Method($this, $method);

            $renderParamsSource = null;
            if ($params) {

                foreach ($params as $key => $value) {
                    if (is_numeric($key)) {
                        if (!isset($reflection->getParameters()[$key])) {
                            continue;
                        }
                        $paramRefl = $reflection->getParameters()[$key];
                        $namedParams[$paramRefl->getName()] = $value;
                    } else {
                        $namedParams[$key] = $value;
                    }
                }

                $renderParamsSource = new ArrayParameterSource($namedParams);
                $this->componentParameters->add($renderParamsSource);
            }

            $loadedParams = [];
            foreach ($reflection->getParameters() as $paramRefl) {
                $loadedParams[$paramRefl->getName()] = $this->getComponentParameter($paramRefl->getName());
            }

            $this->onBeforeRender();

            //Pre-render
            call_user_func_array([$this, $method], $loadedParams);
            //Post-render

            $this->componentRender();

            $this->onAfterRender();

            if ($renderParamsSource) {
                $this->componentParameters->remove($renderParamsSource);
            }
        }, $params);
    }

    /**
     * Method called after execution of any render method.
     */
    protected function componentRender()
    {
        if($this->disableRenderByStatusEntity()) return;
        
        //find template if specified in parameters
        if (!$this->templateFile) {
            $this->setTemplateFile($this->getComponentParameter("template"));
        }

        //include default values into template
        if (!isset($this->template->user)) {
            $this->template->user = $this->user;
        }
        if (!isset($this->template->lang)) {
            $this->template->lang = $this->getPresenter()->lang;
        }

        //render template
        $this->getTemplateFile();
        $this->template->render();
    }

    private function bindContainers()
    {
        if (!$this->hasContainer) {
            return;
        }
        $this->onBeforeRender[] = function() {
            if (!$this->hasContainer) {
                return;
            }
            Helpers::renderContainerStart(Helpers::getContainer($this, self::CONTAINER_DEFAULT, self::PARAM_CONTAINER));
        };
        $this->onAfterRender[] = function() {
            if (!$this->hasContainer) {
                return;
            }
            Helpers::renderContainerEnd(Helpers::getContainer($this, self::CONTAINER_DEFAULT, self::PARAM_CONTAINER));
        };
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
     * Get component parameter or return defualt value if no parameter is found
     * 
     * @param string $parameter Name of parameter
     * @param mixed $default Defualt value if no parameter is found
     * @param IParameterReader|array $parameterReader
     * @return string
     */
    public function getComponentParameterDefault($parameter, $default, $parameterReader = null)
    {
        $param = $this->componentParameters->getParameter($parameter, $parameterReader);
        if ($param) {
            return $param;
        }
        return $default;
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
