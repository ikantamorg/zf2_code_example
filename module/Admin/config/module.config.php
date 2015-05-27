<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Login'           => 'Admin\Controller\Login',
            'Admin\Logout'          => 'Admin\Controller\Logout',
            'Admin\Index'           => 'Admin\Controller\Index',
            'Admin\Dashboard'       => 'Admin\Controller\Dashboard',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => [
                'type' => 'Literal',
                'options' => array(
                    'route' => '/admin',
                    'defaults' => array(
                        'controller' => 'Admin\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'dashboard' => [
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/dashboard[/:action]',
                            'defaults' => array(
                                'controller' => 'Admin\Dashboard',
                                'action' => 'index',
                            ),
                        ),
                    ],
                    'login' => [
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/login[/:action]',
                            'defaults' => array(
                                'controller' => 'Admin\Login',
                                'action' => 'index',
                            ),
                        ),
                    ],
                    'logout' => [
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/logout[/:action]',
                            'defaults' => array(
                                'controller' => 'Admin\Logout',
                                'action' => 'index',
                            ),
                        ),
                    ]
                )
            ]
        )
    ),

    'admin' => [
        'menu' => [
            'dashboard' => array(
                'order' => 0,
                'label' => 'Dashboard',
                'route' => 'admin/dashboard',
                'icon' => 'fa fa-home'
            ),
        ],
        'dashboard_widget' => []
    ],
);
