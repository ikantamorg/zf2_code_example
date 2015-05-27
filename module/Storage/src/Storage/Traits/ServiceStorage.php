<?php
namespace Storage\Traits;

trait ServiceStorage
{
    public function getServiceStorage()
    {
        return $this->getServiceLocator()->get('storage.storage');
    }
}