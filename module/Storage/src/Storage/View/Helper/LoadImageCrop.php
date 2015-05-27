<?php

namespace Storage\View\Helper;

use Core\Traits\DoctrineBasicsTrait;
use Core\Traits\UserTrait;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\View\Helper\Partial;

class LoadImageCrop extends Partial
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
        if(!isset($options['template'])) $options['template'] = 'helper/storage/load_image_crop';
        return parent::__invoke($options['template'], $options);
    }
}