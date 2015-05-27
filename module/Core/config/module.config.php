<?php

namespace Core;

return array(
    'controllers' => array(
        'invokables' => array(
            'Core\Controller\Index' => 'Core\Controller\Index',
            'Core\Widget' => 'Core\Controller\Widget',

            'Admin\Core\General' => 'Core\Controller\Admin\General'
        ),
    ),

    'router' => array(
        'routes' => array(
            'admin' => [
                'child_routes' => [
                    'general-options' => [
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/general-options[/:action]',
                            'defaults' => array(
                                'controller' => 'Admin\Core\General',
                                'action' => 'index',
                            ),
                        )
                    ]
                ]
            ],
            'widget' => [
                'type' => 'Segment',
                'options' => array(
                    'route' => '/widget[/:widget_module][/:widget_name][/:widget_action]',
                    'defaults' => array(
                        'controller' => 'Core\Widget',
                        'action' => 'index',
                        'widget_action' => 'index'
                    ),
                ),
            ],
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Core\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),

    'translator' => array(
        //'locale' => 'ru_RU',
        'locale' => 'en_US',
    ),


    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'core/error/404',
        'exception_template'       => 'core/error/index',
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),

    'asset_manager' => array(
        'resolver_configs' => array_merge_recursive(
            array(
                'collections' => array(
                    'js/core.js' => array(
                        'core/js/form.js',
                        'core/js/table.js',
                        'core/js/main.js',
                    ),
                    'css/core.css' => []
                ),
                'map' => [
                    'core/js/form.js'     => __DIR__ . '/../public/js/form.js',
                    'core/js/table.js'    => __DIR__ . '/../public/js/table.js',
                    'core/js/main.js'     => __DIR__ . '/../public/js/main.js',
                ],
                'caching' => array(
                    'js/core.js'    => ['cache'     => 'Apc'],
                    'css/core.css' =>  ['cache'     => 'Apc'],
                ),
            )
        )
    ),
    'admin' => [
        'menu' => [
            'general-options' => array(
                'order' => 99999,
                'label' => 'General options',
                'route' => 'admin/general-options',
                'icon' => 'fa fa-cog'
            ),
        ]
    ],

    'core' => include __DIR__ . '/config.php'
);
