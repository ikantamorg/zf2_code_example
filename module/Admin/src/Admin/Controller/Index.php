<?php

namespace Admin\Controller;

class Index extends AbstractController
{
    public function indexAction()
    {
        $user = $this->getServiceAuth()->getLoginUser();

        if(!$user){
            return $this->redirect()->toRoute('admin/login');
        }

        if(!$user->inRoleSlug('admin')){
            return $this->redirect()->toRoute('admin/logout');
        }
        return $this->redirect()->toRoute('admin/dashboard');
    }
}
