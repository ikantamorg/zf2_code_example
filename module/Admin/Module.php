<?php

namespace Admin;

use Core\AbstractModule;
use Admin\View\Helper\Menu;
use Admin\View\Helper\Table;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'adminBlockMenu' => function($serviceManager) {
                    return new Menu($serviceManager->getServiceLocator());
                },
                'adminBlockTable' => function($serviceManager) {
                    return new Table($serviceManager->getServiceLocator());
                }

            ]
        ];
    }

}