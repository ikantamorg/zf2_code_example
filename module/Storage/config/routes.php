<?php
return array(
    'storage_test' => [
        'type' => 'Segment',
        'options' => array(
            'route' => '/test-storage[/:action]',
            'defaults' => array(
                'controller' => 'Admin\Storage\Manager',
                'action' => 'index',
            ),
        )
    ],
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
                                'controller' => 'Admin\Storage\Manager',
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
);