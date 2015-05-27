<?php

namespace Admin\Form\Auth;

use Zend\Form\Element;
use Core\Traits\ServiceDoctrine;
use User\Form\Auth\Login as LoginForm;

class Login extends LoginForm
{
    use ServiceDoctrine;

    public function isValid()
    {
        $isValid = parent::isValid();
        if($isValid){
            $user = $this->getServiceDoctrine()->getRepository('User', 'User')->findOneBy(['email' => $this->get('email')->getValue()]);
            if($user->inRoleSlug('admin')){
                return true;
            }
            $this->get('email')->setMessages(['email' => 'permissions']);
        }
        return $isValid;
    }
}