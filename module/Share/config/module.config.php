<?php

namespace Seo;

return array(
    'controllers' => array(
        'invokables' => array(
            'Seo\Sitemap'                   => 'Seo\Controller\Sitemap',

            'Admin\Seo\Options'             => 'Seo\Controller\Admin\Options'
        ),
    ),

    'router' => array(
        'routes' => array(
            'admin' => [
                'child_routes' => [
                    'seo' => [
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/seo'
                        ),
                        'may_terminate' => true,
                        'child_routes' => [
                            'options' => [
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/options[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Admin\Seo\Options',
                                        'action' => 'index',
                                    ),
                                ),
                            ],
                        ]
                    ]
                ]
            ],
            'sitemap' => [
                'type' => 'Literal',
                'options' => array(
                    'route' => '/sitemap.xml',
                    'defaults' => array(
                        'controller' => 'Seo\Sitemap',
                        'action' => 'index',
                    ),
                ),

            ]
        )
    ),

    'admin' => [
        'menu' => [
            'seo' => array(
                'order' => 3,
                'label' => 'Seo',
                'route' => 'admin/seo/options',
                'icon' => 'fa fa-book',
                'pages' => [
                    'options' => [
                        'label' => 'Options',
                        'route' => 'admin/seo/options',
                        'icon' => ''
                    ]
                ]
            ),
        ]
    ]
);
