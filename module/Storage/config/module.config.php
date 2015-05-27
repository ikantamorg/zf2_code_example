<?php
return array(
    'controllers' => array(
        'invokables' => [
            'Admin\Storage\StorageManager'         => 'Storage\Controller\Admin\StorageManager',
            'Admin\Storage\Options'         => 'Storage\Controller\Admin\Options',

            'Storage\Test'                  => 'Storage\Controller\Test',

            'Admin\Storage\Index'           => 'Storage\Controller\Index',
            'Admin\Storage\Edit'            => 'Storage\Controller\Admin\Storage\Edit',
            'Admin\Storage\File\Manager'    => 'Storage\Controller\Admin\File\Manager',
        ],
    ),

    'router' => array(
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'storages' => [
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/storages'
                        ),
                        'may_terminate' => true,
                        'child_routes' => [
                            'manager' => [
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/manager[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Admin\Storage\StorageManager',
                                        'action' => 'index',
                                    ),
                                ),
                            ],
                            'options' => [
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/options[/:action]',
                                    'defaults' => array(
                                        'controller' => 'Admin\Storage\Options',
                                        'action' => 'index',
                                    ),
                                ),
                            ],
                        ]
                    ]
                ]
            ]
        ]
    ),

    'admin' => [
        'menu' => [
            'storage' => array(
                'order' => 2,
                'label' => 'Storage System',
                'route' => 'admin/storages/manager',
                'icon' => 'fa fa-hdd-o',
                'pages' => [
                    'manager' => [
                        'label' => 'Manage Services',
                        'route' => 'admin/storages/manager',
                        'icon' => ''
                    ],
                    'options' => [
                        'label' => 'Options',
                        'route' => 'admin/storages/options',
                        'icon' => ''
                    ]
                ]
            ),
        ]
    ],

    'asset_manager' => array(
        'resolver_configs' => array_merge_recursive(
            array(
                'collections' => array(
                    'js/core.js' => array(
                        'storage/js/jquery.ui.widget.js',
                        'storage/js/jquery.fileupload.js',
                        'storage/js/waitforimages.js',
                        'storage/js/uploadFile.js',
                        'storage/js/index.js'
                    ),
                    'js/admin.js' => array(
                        'storage/js/jquery.ui.widget.js',
                        'storage/js/jquery.fileupload.js',
                        'storage/js/waitforimages.js',
                        'storage/js/uploadFile.js',
                        'storage/js/index.js'
                    ),
                ),
                'map' => array(
                    'storage/js/jquery.ui.widget.js'    => __DIR__ . '/../public/js/jquery.ui.widget.js',
                    'storage/js/jquery.fileupload.js'   => __DIR__ . '/../public/js/jquery.fileupload.js',
                    'storage/js/waitforimages.js'       => __DIR__ . '/../public/js/waitforimages.js',
                    'storage/js/uploadFile.js'          => __DIR__ . '/../public/js/uploadFile.js',
                    'storage/js/index.js'               => __DIR__ . '/../public/js/index.js',
                ),
            )
        )
    )

);