<?php

namespace Seo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Seo\Traits\ServiceSeo;
use Core\Traits\ServiceOption;

class RenderDescription extends AbstractHelper
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
        return '<meta name="description" content="' . $this->getServiceSeo()->getDescriptionString() . '">
    ';
    }
} 