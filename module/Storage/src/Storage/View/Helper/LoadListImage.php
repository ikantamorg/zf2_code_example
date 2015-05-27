<?php

namespace Storage\View\Helper;

use Core\Traits\DoctrineBasicsTrait;
use Core\Traits\UserTrait;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\View\Helper\Partial;

class LoadListImage extends Partial
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
        if(!isset($options['template'])) $options['template'] = "storage/load/list_image";
        return parent::__invoke($options['template'], $options);
    }
}