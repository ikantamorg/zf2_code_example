<?php

namespace Seo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Seo\Traits\ServiceSeo;
use Core\Traits\ServiceOption;

class RenderTitle extends AbstractHelper
{
    use ServiceSeo;
    use ServiceOption;


    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke()
    {
        return '<title>' . $this->getServiceSeo()->getTitleString() . '</title>
    ';
    }
} 