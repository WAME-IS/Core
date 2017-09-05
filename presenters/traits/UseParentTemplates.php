<?php

namespace Wame\Core\Presenters\Traits;


trait UseParentTemplates
{
    protected function getTemplatesFolder()
    {
        $dir = dirname($this->getReflection()->getParentClass()->getFileName());
        $dir = is_dir($dir . DIRECTORY_SEPARATOR . 'templates') ? $dir : dirname($dir);

        $this->setTemplateDir(str_replace(VENDOR_PATH . DIRECTORY_SEPARATOR . PACKAGIST_NAME . DIRECTORY_SEPARATOR, APP_PATH . DIRECTORY_SEPARATOR, $dir));

        return $dir;
    }

    
    protected function getTemplatePresenter()
    {
        return substr($this->getReflection()->getParentClass()->getShortName(), 0, -9);
    }
    
}
