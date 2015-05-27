<?php
namespace Core\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Session\Container as SessionContainer;
use Zend\ServiceManager\ServiceLocatorInterface;

class Session extends AbstractService implements ServiceLocatorAwareInterface
{

    protected $_session;


    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->_session = new SessionContainer('core');
        parent::__construct($serviceLocator);
    }

    public function getValue($name)
    {
        return $this->_session->{$name};
    }

    public function setValue($name, $value)
    {
        $this->_session->{$name} = $value;
        return $this;
    }

    public function get($name = null)
    {
        if($name){
            return new SessionContainer($name);
        } else {
            return $this->_session;
        }
    }
}