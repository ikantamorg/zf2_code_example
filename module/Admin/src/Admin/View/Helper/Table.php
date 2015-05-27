<?php

namespace Admin\View\Helper;

use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\View\Helper\Partial;

class Table extends Partial
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

    public function __invoke($params = [])
    {
        return parent::__invoke('helper/admin/table', $params);
    }
}