<?php

namespace Seo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Seo\Traits\ServiceSeo;
use Core\Traits\ServiceOption;

class RenderHead extends AbstractHelper
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
        $viewHelpers = $this->getServiceLocator()->get('viewhelpermanager');

        $renderTitle = $viewHelpers->get('renderTitle');
        $renderDescription = $viewHelpers->get('renderDescription');
        $renderKeywords = $viewHelpers->get('renderKeywords');
        $renderMeta = $viewHelpers->get('renderMeta');

        $head = $renderTitle();
        $head .= $renderDescription();
        $head .= $renderKeywords();
        $head .= $renderMeta();

        return $head;
    }
} 