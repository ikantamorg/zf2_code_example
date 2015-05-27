<?php
namespace Core\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractService implements ServiceLocatorAwareInterface {

    protected static $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator()
    {
        return self::$serviceLocator;
    }

    public static function setServiceLocatorStatic(&$serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    public static function getServiceLocatorStatic()
    {
        return self::$serviceLocator;
    }



}