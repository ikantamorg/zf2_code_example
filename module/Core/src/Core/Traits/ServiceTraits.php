<?php
namespace Core\Traits;


use Core\Service\AbstractService;

trait ServiceTraits
{

    public function getServiceLocator()
    {
        return AbstractService::getServiceLocatorStatic();
    }

}