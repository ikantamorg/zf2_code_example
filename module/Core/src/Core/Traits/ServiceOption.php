<?php
namespace Core\Traits;

trait ServiceOption
{

    public function getServiceOption()
    {
        return $this->getServiceLocator()->get('core.option');
    }

}