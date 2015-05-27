<?php

return array(
    'layout/admin'                  => __DIR__ . '/layout/index.phtml',
    'layout/auth'                   => __DIR__ . '/layout/auth.phtml',

    'helper/admin/menu'             => __DIR__ . '/helper/menu.phtml',
    'helper/admin/pagination'       => __DIR__ . '/helper/pagination.phtml',
    'helper/admin/table'            => __DIR__ . '/helper/table.phtml',

    'block/admin/count_to_page'     => __DIR__ . '/block/count_to_page.phtml',

    'admin/login'                   => __DIR__ . '/login.phtml',
    'admin/dashboard'               => __DIR__ . '/dashboard.phtml'
);

$this->translate("Options");
$this->translate("Dashboard");
$this->translate("Manager");