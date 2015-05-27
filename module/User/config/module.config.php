<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'User\Auth\Login'               => 'User\Controller\Auth\Login',
            'User\Auth\Logout'              => 'User\Controller\Auth\Logout',
            'User\Auth\Registration'        => 'User\Controller\Auth\Registration',
            'User\Auth\Social'              => 'User\Controller\Auth\Social',

            'Admin\User\UserManager'        => 'User\Controller\Admin\UserManager',
            'Admin\User\RoleManager'        => 'User\Controller\Admin\RoleManager',
            'Admin\User\AuthSocial'         => 'User\Controller\Admin\AuthSocial',

            'Widget\User\AdminDashboard'    => 'User\Widget\AdminDashboard'
        ),
    ),
    'router' => array(
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'users' => [
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/users'
                        ),
                        'may_terminate' => true,
                        'child_routes' => [
                            'manager' => [
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/manager[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Admin\User\UserManager',
                                        'action' => 'index',
                                    ),
                                )
                            ],
                            'options' => [
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/options[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Admin\User\RoleManager',
                                        'action' => 'index',
                                    ),
                                )
                            ],
                            'socials' => [
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/socials[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Admin\User\AuthSocial',
                                        'action' => 'index',
                                    ),
                                )
                            ]
                        ]
                    ]
                ]
            ],

            'login' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/login[/:action]',
                    'defaults' => array(
                        'controller' => 'User\Auth\Login',
                        'action' => 'index',
                    ),
                )
            ],
            'logout' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/logout[/:action]',
                    'defaults' => array(
                        'controller' => 'User\Auth\Logout',
                        'action' => 'index',
                    ),
                )
            ],
            'registration' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/registration[/:action]',
                    'defaults' => array(
                        'controller' => 'User\Auth\Registration',
                        'action' => 'index',
                    ),
                )
            ]
        ]
    ),

    'admin' => [
        'menu' => [
            'user' => array(
                'order' => 1,
                'label' => 'Members',
                'route' => 'admin/users/manager',
                'icon' => 'fa fa-user',
                'pages' => [
                    'manager' => [
                        'label' => 'Manage Members',
                        'route' => 'admin/users/manager',
                        'icon' => 'glyphicon glyphicon-user'
                    ],
                    'options' => [
                        'label' => 'Options',
                        'route' => 'admin/users/options',
                        'icon' => 'glyphicon glyphicon-user'
                    ],
                    'socials' => [
                        'label' => 'Socials Auth',
                        'route' => 'admin/users/socials',
                        'icon' => 'glyphicon glyphicon-user'
                    ]
                ]
            ),
        ],
        'dashboard_widget' => [
            [
                'module' => 'User',
                'widget_name' => 'AdminDashboard'
            ]
        ]
    ],
    'user' => include __DIR__ . '/config.php'
);
