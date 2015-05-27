<?php

namespace User\Form\Admin;

use Zend\Form\Element;
use Core\Traits\ServiceDoctrine;
use Core\Form\AbstractForm;
use Zend\InputFilter\Factory as InputFactory;

class ResetPasswordUser extends AbstractForm
{
    use ServiceDoctrine;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

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

        $this->setInputFilter($filter);
    }
}