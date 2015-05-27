<?php
namespace Core\Traits;

trait ServiceBrowser {

    public function getServiceBrowser()
    {
        return $this->getServiceLocator()->get('core.browser');
    }
}