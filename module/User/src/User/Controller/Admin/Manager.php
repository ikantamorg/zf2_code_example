<?php
namespace User\Controller\Admin;

use Admin\Controller\AbstractController as CoreController;
use Zend\View\Model\JsonModel;
use User\Traits\ServiceUser;

class Manager extends CoreController
{
    use ServiceUser;

    public function indexAction()
    {
        $form = new \User\Form\Admin\CreateUser();

        $params = [
            'formCreate' => $form,
            'roles' => $this->getServiceDoctrine()->getRepository('User', 'Role')->findAll(),
            'template_paginator' => 'helper/admin/pagination',
            'template_main' => 'admin/user/manager',
            'module' => 'User',
            'model' => 'User'
        ];
        return $this->itable($params);
    }

    public function createAction()
    {
        $data = ['success' => false];
        $formRegistration = new \User\Form\Admin\CreateUser();
        $formRegistration->setData($this->params()->fromPost());
        if($formRegistration->isValid()){

            $formData = $formRegistration->getData();
            $formData['role_slug'] = $this->params()->fromPost('role_slug');

            $this->getServiceUser()->createUser($formData);
            $data['success_alert'] = 'User successfully added.';
            $data['success'] = true;
        } else {
            $data['errors'] = $formRegistration->getMessages();
        }
        return new JsonModel($data);
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

    public function loadEditAction()
    {
        $data = ['success' => true];
        $user = $this->getServiceDoctrine()->getEntity('User', 'User', $this->params()->fromPost('object_id'));
        $editForm = new \User\Form\Admin\EditUser();
        $resetPasswordForm = new \User\Form\Admin\ResetPasswordUser();

        $editForm->setData([
            'email' => $user->getEmail(),
            'object_id' => $user->getId()
        ]);
        $data['html'] = [];
        $data['html']['form'] = $this->partialHtml('admin/user/edit', [
            'editForm' => $editForm,
            'resetPasswordForm' => $resetPasswordForm
        ]);

        return new JsonModel($data);
    }

    public function saveEditAction()
    {
        $data = ['success' => false];
        $editForm = new \User\Form\Admin\EditUser();
        $editForm->setData($this->params()->fromPost());
        if($editForm->isValid()){
            $formData = $editForm->getData();
            $user = $this->getServiceDoctrine()->getEntity('User', 'User', $this->params()->fromPost('object_id'));
            $user->setEmail($formData['email']);
            $user->save();
            $data['success'] = true;
        } else {
            $data['errors'] = $editForm->getMessages();
        }
        return new JsonModel($data);
    }
}