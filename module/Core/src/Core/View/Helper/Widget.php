<?php

namespace Core\View\Helper;

use Zend\View\Helper\Partial;

class Widget extends Partial
{
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke($module, $widgetName, $options = [])
    {
        $options['action'] = 'index';
        $forward = $this->getServiceLocator()->get('ControllerPluginManager')->get('forward');
        $viewModel = $forward->dispatch('Widget\\' . $module . '\\' . $widgetName, $options);
        return $this->getView()->render($viewModel);
    }
}