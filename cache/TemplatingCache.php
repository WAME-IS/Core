<?php

namespace Wame\Core\Cache;

use Nette\Application\UI\Control;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\ComponentModel\Container;
use Nette\InvalidArgumentException;
use Nette\Object;
use Wame\Core\Components\BaseControl;

class TemplatingCache extends Object
{

    /** @var Cache */
    private $cache;

    /** @var array */
    private $settings;

    /** @var string */
    private $name;

    /** @var boolean */
    private $enabled = false;

    /**
     * @param IStorage $cacheStorage
     * @param string $name
     */
    public function __construct(IStorage $cacheStorage, $name = null)
    {
        $this->cache = new Cache($cacheStorage, "Wame.Templating.Cache");
        $this->settings = [];
        $this->name = $name;
    }

    /**
     * @param string $name
     */
    function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * @param string $tag
     */
    public function addTag($tag)
    {
        $this->addSettings([Cache::TAGS => [$tag]]);
        return $this;
    }

    /**
     * @param string $expiration
     */
    public function setExpiration($expiration)
    {
        $this->addSettings([Cache::EXPIRATION => $expiration]);
        return $this;
    }

    /**
     * @param array $settings
     */
    public function addSettings($settings)
    {
        $this->settings = array_merge_recursive($this->settings, $settings);
        $this->enable();
        return $this;
    }

    /**
     * Enable caching of this template
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * The most important function. Call this function to cache result of render.
     * 
     * @param Control $control
     * @param callable $callback
     * @param array $args
     */
    public function cachedOutput($control, $callback, $args = null)
    {
        $this->addChildCaches($control);

        if (!$this->name) {
            throw new InvalidArgumentException("Name has to be set.");
        }

        if ($this->enabled) {
            if ($active = $this->cache->start($this->name)) {
                if ($args) {
                    call_user_func_array($callback, $args);
                } else {
                    call_user_func($callback);
                }
                $active->end($this->settings);
            }
        } else {
            if ($args) {
                call_user_func_array($callback, $args);
            } else {
                call_user_func($callback);
            }
        }
    }

    /**
     * @param Control $control
     */
    private function addChildCaches($control)
    {
        if ($control instanceof Container) {
            foreach ($control->getComponents() as $subcontrol) {
                if ($subcontrol instanceof BaseControl) {
                    $name = $subcontrol->getComponentCache()->getName();
                    $this->addSettings([
                        Cache::ITEMS => [$name]
                    ]);
                }
            }
        }
    }
}
