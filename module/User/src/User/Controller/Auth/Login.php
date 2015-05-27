<?php
namespace User\Controller\Auth;

use Core\Controller\Core as CoreController;
use Zend\View\Model\JsonModel;

class Login extends CoreController
{
    public function indexAction()
    {
        $formLogin = new \User\Form\Auth\Login();
        return $this->render('user/auth/login', [
            'formLogin' => $formLogin
        ]);
    }

    public function ajaxAction()
    {
        $formLogin = new \User\Form\Auth\Login();
        $formLogin->setData($this->params()->fromPost());
        if($formLogin->isValid()){
            $data['success'] = true;
        } else {
            $data['errors'] = $formLogin->getMessages();
        }
        return new JsonModel($data);
    }
}