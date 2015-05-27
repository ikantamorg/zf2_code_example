<?php

namespace User\View\Helper;

use Zend\View\Helper\Partial;

class GetSocialsProviders extends Partial
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
        $list = $this->getServiceLocator()->get('config')['user']['social_auth']['list'];
        $providers = [];
        foreach($list as $index=>&$provider){
            if(!$provider['enabled']) continue;
            $providers[] = $index;
        }
        return $providers;
    }
}