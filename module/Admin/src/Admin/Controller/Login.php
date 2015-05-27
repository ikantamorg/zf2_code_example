<?php

namespace Admin\Controller;

use Zend\View\Model\JsonModel;
use User\Traits\ServiceAuth;
use Core\Controller\Core;

class Login extends Core
{
    use ServiceAuth;

    public function init()
    {
        $this->layout('layout/auth');
    }

    public function indexAction()
    {
        $user = $this->getServiceAuth()->getLoginUser();
        if($user){
            if($user->inRoleSlug('admin')){
                return $this->redirect()->toRoute('admin/dashboard');
            } else {
                return $this->redirect()->toRoute('admin/logout');
            }
        }
        $formLogin = new \Admin\Form\Auth\Login();
        return $this->render('admin/login', ['formLogin' => $formLogin]);
    }

    public function ajaxAction()
    {
        $data = ['success' => false];
        $formLogin = new \Admin\Form\Auth\Login();
        $formLogin->setData($this->params()->fromPost());
        if($formLogin->isValid()){
            $user = $this->getServiceDoctrine()->getRepository('User', 'User')->findOneBy(['email' => $formLogin->getData()['email']]);
            $this->getServiceAuth()->authorize($user);
            $data['redirect'] = $this->url()->fromRoute('admin/dashboard');
            $data['success'] = true;
        } else {
            $data['errors'] = $formLogin->getMessages();
        }
        return new JsonModel($data);
    }
}
