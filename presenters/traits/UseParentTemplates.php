<?php

namespace Wame\Core\Presenters\Traits;


trait UseParentTemplates
{
    protected function getTemplatesFolder()
    {
        $dir = dirname($this->getReflection()->getParentClass()->getFileName());

        if (is_dir($dir . DIRECTORY_SEPARATOR . 'templates')) {
            $dir = $dir;
        } else {
            $dir = dirname($dir) . DIRECTORY_SEPARATOR . 'presenters';
        }

        $this->setTemplateDir(str_replace(
            VENDOR_PATH . DIRECTORY_SEPARATOR . PACKAGIST_NAME . DIRECTORY_SEPARATOR,
            APP_PATH . DIRECTORY_SEPARATOR,
            $dir . DIRECTORY_SEPARATOR . 'templates'
        ));

        return $dir;
    }

    
    protected function getTemplatePresenter()
    {
        return substr($this->getReflection()->getParentClass()->getShortName(), 0, -9);
    }
    
}
