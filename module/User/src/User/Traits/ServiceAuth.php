<?php
namespace User\Traits;

trait ServiceAuth
{
    public function getServiceAuth()
    {
        return $this->getServiceLocator()->get('user.auth');
    }
}