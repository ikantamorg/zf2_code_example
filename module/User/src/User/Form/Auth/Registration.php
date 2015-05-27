<?php

namespace User\Form\Auth;

use Zend\Form\Element;
use Core\Form\AbstractForm;
use Core\Traits\ServiceDoctrine;
use Zend\InputFilter\Factory as InputFactory;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;

class Registration extends AbstractForm
{
    use ServiceDoctrine;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(new Element\Email('email', ['label' => 'Email']));
        $this->add(new Element\Password('password', ['label' => 'Password']));
        $this->add(new Element\Password('confirm_password', ['label' => 'Confirm password']));

        $factory     = new InputFactory();
        $filter = $this->getInputFilter();

        $filter->add($factory->createInput([
            'name' => 'password',
            'required' => true
        ]));

        $filter->add($factory->createInput([
            'name' => 'confirm_password',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    ),
                ),
            )
        ]));

        $emailFilter = $filter->get('email');

        $noObjectExistsValidator = new NoObjectExistsValidator(array(
            'object_repository' => $this->getServiceDoctrine()->getRepository('User', 'User'),
            'fields'            => 'email'
        ));

        $emailFilter->getValidatorChain()->attach($noObjectExistsValidator);

        $this->setInputFilter($filter);
    }
}