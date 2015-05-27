<?php
namespace Admin\Controller;

use Core\Controller\Core as CoreController;
use Zend\Mvc\MvcEvent;
use User\Traits\ServiceAuth;

class AbstractController extends CoreController
{
    use ServiceAuth;


    public function init()
    {
        $this->layout('layout/admin');
    }

    public function onDispatch(MvcEvent $e)
    {
        $user = $this->getServiceAuth()->getLoginUser();

        if(!$user){
            return $this->redirect()->toRoute('admin/login');
        }

        if(!$user->inRoleSlug('admin')){
            return $this->redirect()->toRoute('admin/logout');
        }
        return parent::onDispatch($e);
    }

}