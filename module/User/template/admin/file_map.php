<?php

return  array(
    'collections' => array(
        'js/admin.js' => [
            'user/js/admin.js'
        ],
        'css/admin.css' => [
            'user/css/admin.css'
        ],
    ),
    'map' => [
        'user/js/admin.js'          =>__DIR__ . '/public/js/admin.js',

        'user/css/admin.css'        =>__DIR__ . '/public/css/index.css'
    ]
);
