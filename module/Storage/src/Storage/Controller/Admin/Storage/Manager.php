<?php
namespace Storage\Controller\Admin\Storage;

use Admin\Controller\AbstractController;
use Zend\View\Model\JsonModel;
use Storage\Traits\ServiceStorage;

class Manager extends AbstractController
{
    use ServiceStorage;


    public function indexAction()
    {
        $params = [
            'template_paginator' => 'helper/admin/pagination',
            'template_main' => 'admin/storage/manager',
            'module' => 'Storage',
            'model' => 'Storage',
            'create_form' => new \Storage\Form\Admin\CreateStorage(),
            'cols' => ['#', 'Name', 'Type', 'Size']
        ];
        return $this->itable($params);
    }


    public function createAction()
    {
        $data = ['success' => false];
            
        $form = new \Storage\Form\Admin\CreateStorage();
        $form->setData($this->params()->fromPost());

        if($form->isValid()){
            $formData = $form->getData();
            $this->getServiceStorage()->createStorage($formData);
            $data['success_alert'] = 'Storage successfully added.';
            $data['success'] = true;
        } else {
            $data['errors'] = $form->getMessages();
        }

        return new JsonModel($data);
    }
}
