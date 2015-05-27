<?php
namespace User\Controller\Auth;

use Core\Controller\Core as CoreController;
use Zend\View\Model\JsonModel;
use User\Traits\ServiceUser;

class Registration extends CoreController
{
    use ServiceUser;

    public function indexAction()
    {
        $formRegistration = new \User\Form\Auth\Registration();
        return $this->render('user/auth/registration', [
            'formRegistration' => $formRegistration
        ]);
    }

    public function ajaxAction()
    {
        $data = ['success' => false];
        $formRegistration = new \User\Form\Auth\Registration();
        $formRegistration->setData($this->params()->fromPost());
        if($formRegistration->isValid()){
            $this->getServiceUser()->createUser($formRegistration->getData());
            $data['success'] = true;
        } else {
            $data['errors'] = $formRegistration->getMessages();
        }
        return new JsonModel($data);
    }
}