<?php

namespace User\Form\Admin;

use Zend\Form\Element;
use Core\Traits\ServiceDoctrine;
use Core\Form\AbstractForm;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;

class EditRole extends CreateRole
{
    use ServiceDoctrine;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(new Element\Hidden('object_id'));
    }
}