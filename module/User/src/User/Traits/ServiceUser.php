<?php
namespace User\Traits;

trait ServiceUser {

    public function getServiceUser()
    {
        return $this->getServiceLocator()->get('user.user');
    }
}