<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class BaseUrl extends AbstractHelper{

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
        return rtrim('http://' . $_SERVER['HTTP_HOST'], '/');
    }
} 