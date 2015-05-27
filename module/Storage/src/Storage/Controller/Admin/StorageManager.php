<?php
namespace Storage\Controller\Admin;

use Admin\Controller\AbstractController;
use Zend\View\Model\JsonModel;
use Storage\Traits\ServiceStorage;

class StorageManager extends AbstractController
{
    use ServiceStorage;


    public function indexAction()
    {
        $params = [
            'create_form'           => new \Storage\Form\Admin\CreateStorage(),
            'template_paginator'    => 'helper/admin/pagination',
            'template_main'         => 'admin/storage/manager',
            'module'                => 'Storage',
            'model'                 => 'Storage',
            'cols'                  => ['#', 'Name', 'Type', 'Size']
        ];
        return $this->itable($params);
    }

    protected function _create($data)
    {
        $this->getServiceStorage()->createStorage($data);
    }
}
