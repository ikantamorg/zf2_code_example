<?php
namespace Core\Traits;

trait ServiceSession
{
    public function getServiceSession()
    {
        return $this->getServiceLocator()->get('core.session');
    }
}