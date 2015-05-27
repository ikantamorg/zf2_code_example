<?php

namespace Share;

use Core\AbstractModule;
use Seo\Service\Seo;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;


    public function getServiceConfig()
    {
        return array(
            'factories' => array(

            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(

            ),
        );
    }
}
