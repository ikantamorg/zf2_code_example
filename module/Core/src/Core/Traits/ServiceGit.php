<?php
namespace Core\Traits;

trait ServiceGit {

    public function getServiceGit()
    {
        return $this->getServiceLocator()->get('core.git');
    }
}