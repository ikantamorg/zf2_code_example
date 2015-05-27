<?php
namespace User\Controller\Admin;

use Admin\Controller\AbstractController as CoreController;
use Zend\View\Model\JsonModel;
use User\Traits\ServiceUser;

class RoleManager extends CoreController
{
    use ServiceUser;

    public function indexAction()
    {
        $defaultAvatar = $this->getServiceDoctrine()->getEntity('User', 'Avatar', $this->getServiceOption()->get('user', 'default_avatar_id')->getValue());

        $form = new \User\Form\Admin\Options();
        $form->setData([
            'default_avatar_id' => $defaultAvatar ? $defaultAvatar->getId() : '',
            'default_role_id' => $this->getServiceOption()->get('user', 'default_role_id')->getValue(),
            'default_storage_id' => $this->getServiceOption()->get('user', 'default_storage_id')->getValue()
        ]);

        $params = [
            'edit_form'             => new \User\Form\Admin\EditRole(),
            'create_form'           => new \User\Form\Admin\CreateRole(),
            'template_edit'         => 'admin/user/role/edit',
            'template_paginator'    => 'helper/admin/pagination',
            'template_main'         => 'admin/user/role/manager',
            'module'                => 'User',
            'model'                 => 'Role',
            'cols'                  => ['#', 'Name', 'Slug', 'Actions'],
            'defaultAvatar' => $defaultAvatar,
            'form' => $form,
        ];

        return $this->itable($params);
    }

    protected function _create($data)
    {
        $this->getServiceUser()->createRole($data);
    }

    protected function _loadEdit($object, $form)
    {
        $form->setData([
            'name' => $object->getName(),
            'object_id' => $object->getId()
        ]);
    }

    protected function _edit($data)
    {
        $slug = $this->getServiceLocator()->get('Seo\Slug');
        $user = $this->getServiceDoctrine()->getEntity('User', 'Role', $this->params()->fromPost('object_id'));
        $user->setName($data['name']);
        $user->setSlug($slug->create($data['name']));
        $user->save();
    }

    public function uploadAvatarAction()
    {
        $data = ['success' => false];
        $files = $this->getRequest()->getFiles();
        $avatar = $this->getServiceUser()->createAvatar($files->file);
        $data['href'] = $avatar->getMiddleFile()->getHref();
        $data['default_avatar_id'] = $avatar->getId();
        $data['success'] = true;

        return new JsonModel($data);
    }

    public function ajaxAction()
    {
        $data = ['success' => false];
        $post = $this->params()->fromPost();
        $form = new \User\Form\Admin\Options();
        $form->setData($post);
        if($form->isValid()){
            $formData = $form->getData();
            $lastDefaultAvatar = $this->getServiceDoctrine()->getEntity('User', 'Avatar', $this->getServiceOption()->get('user', 'default_avatar_id')->getValue());
            $defaultAvatar = $this->getServiceDoctrine()->getEntity('User', 'Avatar', $formData['default_avatar_id']);
            $defaultAvatar->setIsTmp(0);
            $defaultAvatar->save();

            if($lastDefaultAvatar && $lastDefaultAvatar->getId() != $defaultAvatar->getId()){
                $lastDefaultAvatar->delete();
            }

            $this->getServiceOption()->get('user', 'default_avatar_id')->setValue($formData['default_avatar_id'])->save();
            $this->getServiceOption()->get('user', 'default_storage_id')->setValue($formData['default_storage_id'])->save();
            $this->getServiceOption()->get('user', 'default_role_id')->setValue($formData['default_role_id'])->save();
            $data['success_alert'] = 'Changes saved successfully';
            $data['success'] = true;
        } else {
            $data['errors'] = $form->getMessages();
        }
        return new JsonModel($data);
    }
}