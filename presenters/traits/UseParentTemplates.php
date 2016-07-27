<?php

namespace Wame\Core\Presenters\Traits;

trait UseParentTemplates
{
    
    protected function getTemplatesFolder()
    {
        $dir = dirname($this->getReflection()->getParentClass()->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        return $dir;
    }
    
    protected function getTemplatePresenter()
    {
        return substr($this->getReflection()->getParentClass()->getShortName(), 0, -9);
    }
    
}
