<?php

namespace User\View\Helper;

use Zend\View\Helper\Partial;

class IsLogin extends Partial
{
    use \User\Traits\ServiceAuth;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke($options = [])
    {
        return $this->getServiceAuth()->getLoginUser() ? true : false;
    }
}