<?php

namespace Admin\Controller;

use Zend\View\Model\JsonModel;
use User\Traits\ServiceAuth;

class Logout extends AbstractController
{
    use ServiceAuth;

    public function indexAction()
    {
        $this->getServiceAuth()->logout();
        return $this->redirect()->toRoute('admin/login');
    }

    public function ajaxAction()
    {
        $this->getServiceAuth()->logout();
        $data = [
            'success' => true,
            'redirect' => $this->url()->fromRoute('admin/login')
        ];
        return new JsonModel($data);
    }
}
