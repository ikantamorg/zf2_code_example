<?php

namespace User\Form\Admin;

use Admin\Form\AbstractForm as Form;
use Zend\Form\Element;

class Options extends Form
{
    use \Core\Traits\ServiceDoctrine;


    public function __construct()
    {
        parent::__construct();
        $this->add(new \Storage\Form\Element\Storage('default_storage_id', ['label' => 'Default Sender']));
        $this->add(new Element\Textarea('default_avatar_id', ['label' => 'Share image']));

        $roles = $this->getServiceDoctrine()->getRepository('User', 'Role')->findAll();
        $arrayRoles = [];
        foreach($roles as $_role){
            $arrayRoles[$_role->getId()] = $_role->getName();
        }

        $this->add(new Element\Select('default_role_id', ['options' => $arrayRoles]));
    }
}