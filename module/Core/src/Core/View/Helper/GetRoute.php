<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetRoute extends AbstractHelper{

    protected $route;

    public function __construct($route)
    {
        $this->route = $route;
    }

    public function getController()
    {
        return $this->route->getParam('controller');
    }

    public function getAction()
    {
        return $this->route->getParam('action');
    }

    public function __invoke()
    {
        return $this;
    }
} 