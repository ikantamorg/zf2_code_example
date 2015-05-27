<?php

namespace User\Form\Admin;

use Zend\Form\Element;
use Core\Traits\ServiceDoctrine;
use Core\Form\AbstractForm;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;

class EditUser extends AbstractForm
{
    use ServiceDoctrine;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(new Element\Hidden('object_id'));
        $this->add(new Element\Email('email'));

        $filter = $this->getInputFilter();
        $emailFilter = $filter->get('email');
        $noObjectExistsValidator = new NoObjectExistsValidator(array(
            'object_repository' => $this->getServiceDoctrine()->getRepository('User', 'User'),
            'fields'            => 'email'
        ));
        $emailFilter->getValidatorChain()->attach($noObjectExistsValidator);
        $this->setInputFilter($filter);
    }
}