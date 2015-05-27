<?php
namespace Core\Traits;

trait ServiceDoctrine {

    public function getServiceDoctrine()
    {
        return $this->getServiceLocator()->get('core.doctrine');
    }
}