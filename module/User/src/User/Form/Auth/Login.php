<?php

namespace User\Form\Auth;

use Zend\Form\Element;
use Core\Form\AbstractForm;
use Core\Traits\ServiceDoctrine;
use Zend\InputFilter\Factory as InputFactory;
use DoctrineModule\Validator\ObjectExists as ObjectExistsValidator;
use Zend\Crypt\Password\Bcrypt;

class Login extends AbstractForm
{
    use ServiceDoctrine;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(new Element\Email('email', ['label' => 'Email']));
        $this->add(new Element\Password('password', ['label' => 'Password']));

        $factory = new InputFactory();
        $filter = $this->getInputFilter();

        $filter->add($factory->createInput([
            'name' => 'password',
            'required' => true
        ]));

        $emailFilter = $filter->get('email');

        $noObjectExistsValidator = new ObjectExistsValidator(array(
            'object_repository' => $this->getServiceDoctrine()->getRepository('User', 'User'),
            'fields'            => 'email'
        ));

        $emailFilter->getValidatorChain()->attach($noObjectExistsValidator);
        $this->setInputFilter($filter);
    }

    public function isValid()
    {
        $isValid = parent::isValid();
        if($isValid){
            $user = $this->getServiceDoctrine()->getRepository('User', 'User')->findOneBy(['email' => $this->get('email')->getValue()]);
            $bcrypt = new Bcrypt();
            if(!$bcrypt->verify($this->get('password')->getValue(), $user->getPassword())){
                $this->get('password')->setMessages(['bad' => 'password']);
                return false;
            }
        }
        return $isValid;
    }
}