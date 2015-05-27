<?php

namespace Storage\View\Helper;

use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\View\Helper\Partial;

class LoadImage extends Partial
{

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke($options = [])
    {
        return parent::__invoke('helper/storage/load_image', $options);
    }
}