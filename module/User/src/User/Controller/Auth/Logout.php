<?php
namespace User\Controller\Auth;

use Core\Controller\Core as CoreController;
use Zend\View\Model\JsonModel;

class Logout extends CoreController
{
    use \User\Traits\ServiceAuth;


    public function indexAction()
    {
        $this->getServiceAuth()->logout();
        return $this->redirect()->toRoute('home');

    }
}