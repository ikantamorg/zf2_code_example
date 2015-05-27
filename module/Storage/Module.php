<?php

namespace Storage;

use Core\AbstractModule;
use Storage\Service\Storage;
use Storage\View\Helper\LoadImage;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;


    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'storage.storage' => function($serviceManager){
                    return new Storage($serviceManager);
                },
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'loadImage' => function($serviceManager) {
                    return new LoadImage($serviceManager->getServiceLocator());
                }
            ),
        );
    }
}