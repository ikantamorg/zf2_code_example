<?php

namespace User;

use Core\AbstractModule;
use User\Service\User;
use User\Service\Auth;
use User\View\Helper\GetLoginUser;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'user.user' => function($serviceManager){
                    return new User($serviceManager);
                },
                'user.auth' => function($serviceManager){
                    return new Auth($serviceManager);
                }
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'getLoginUser' => function($serviceManager) {
                    return new GetLoginUser($serviceManager->getServiceLocator());
                },
                'isLogin' => function($serviceManager) {
                    return new \User\View\Helper\IsLogin($serviceManager->getServiceLocator());
                },
                'getSocialsProviders' => function($serviceManager) {
                    return new \User\View\Helper\GetSocialsProviders($serviceManager->getServiceLocator());
                }
            ),
        );
    }


}