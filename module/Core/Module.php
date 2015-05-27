<?php

namespace Core;

require_once 'AbstractModule.php';

use Core\Service\Doctrine;
use Core\Service\Options;
use Core\Service\Resource;
use Core\Service\Session;
use Core\Service\AbstractService;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('viewhelpermanager')->setFactory('getRoute', function($serviceManager) use ($e) {
            $viewHelper = new View\Helper\GetRoute($e->getRouteMatch());
            return $viewHelper;
        });

        AbstractService::setServiceLocatorStatic($e->getApplication()->getServiceManager());

        $doctrine = $e->getApplication()->getServiceManager()->get('core.doctrine');

        try{
            $timezoneOption = $doctrine->getRepository('Core', 'Option')->findOneBy([
                'module' => 'core',
                'name' => 'default_timezone'
            ]);

            if($timezoneOption) {
                date_default_timezone_set($timezoneOption->getValue());
            } else {
                date_default_timezone_set('UTC');
            }

        } catch (\Exception $err){
            date_default_timezone_set('UTC');
        }

        parent::onBootstrap($e);

    }

    public function getView()
    {

    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'paginationHelper' => function($serviceManager) {
                    return new \Core\View\Helper\PaginationHelper($serviceManager->getServiceLocator());
                },
                'rusDate' => function($serviceManager) {
                    return new \Core\View\Helper\RusDate($serviceManager->getServiceLocator());
                },
                'widget' => function($serviceManager) {
                    return new \Core\View\Helper\Widget($serviceManager->getServiceLocator());
                },
                'baseUrl' => function($serviceManager) {
                    return new \Core\View\Helper\BaseUrl($serviceManager->getServiceLocator());
                }
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'core.option' => function($serviceManager){
                    return new Options($serviceManager);
                },
                'core.resource' => function($serviceManager){
                    return new Resource($serviceManager);
                },
                'core.doctrine' => function($serviceManager){
                    return new Doctrine($serviceManager);
                },
                'core.session' => function($serviceManager) {
                    return new Session($serviceManager);
                },
                'core.browser' => function($serviceManager) {
                    return new \Core\Service\Browser($serviceManager);
                },
                'core.git' => function($serviceManager) {
                    return new \Core\Service\Git($serviceManager);
                },
            )
        );
    }
}
