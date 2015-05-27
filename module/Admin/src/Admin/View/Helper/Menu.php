<?php

namespace Admin\View\Helper;

use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\View\Helper\Partial;

class Menu extends Partial
{
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->config = $this->getServiceLocator()->get('config')['admin'];
    }

    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke($options =[])
    {
        $adminMenu = $this->config['menu'];
        $factory = new ConstructedNavigationFactory($adminMenu);
        $navigation = $factory->createService($this->getServiceLocator());
        return parent::__invoke('helper/admin/menu', ['menu' => $navigation]);
    }
}