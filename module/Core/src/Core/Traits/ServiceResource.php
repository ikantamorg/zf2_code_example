<?php
namespace Core\Traits;

trait ServiceResource
{

    public function getServiceResource()
    {
        return $this->getServiceLocator()->get('core.resource');
    }

}