<?php
namespace User\Controller\Admin;

use Admin\Controller\AbstractController as CoreController;
use Zend\View\Model\JsonModel;
use User\Traits\ServiceUser;

class UserManager extends CoreController
{
    use ServiceUser;

    public function indexAction()
    {
        $params = [
            'edit_form'             => new \User\Form\Admin\EditUser(),
            'create_form'           => new \User\Form\Admin\CreateUser(),
            'template_edit'         => 'admin/user/edit',
            'template_paginator'    => 'helper/admin/pagination',
            'template_main'         => 'admin/user/manager',
            'module'                => 'User',
            'model'                 => 'User',
            'cols'                  => ['#', 'Email', 'Created at', 'Last activity', 'Actions']
        ];
        return $this->itable($params);
    }

    protected function _create($data)
    {
        $this->getServiceUser()->createUser($data);
    }

    protected function _loadEdit($object, $form)
    {
        $form->setData([
            'email' => $object->getEmail(),
            'object_id' => $object->getId()
        ]);
    }

    protected function _edit($data)
    {
        $user = $this->getServiceDoctrine()->getEntity('User', 'User', $this->params()->fromPost('object_id'));
        $user->setEmail($data['email']);
        $user->save();
    }

    public function lockedAction()
    {
        $data = ['success' => true];
        $user = $this->getServiceDoctrine()->getEntity('User', 'User', $this->params()->fromPost('object_id'));
        if($user->getIsLocked()){
            $user->unlock();
        } else {
            $user->lock();
        }
        $data['user_status'] = $user->getIsLocked();
        return new JsonModel($data);
    }
}