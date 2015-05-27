<?php

namespace User\Form\Admin;

use Zend\Form\Element;
use Core\Traits\ServiceDoctrine;

class CreateUser extends \User\Form\Auth\Registration
{
    use ServiceDoctrine;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setLabel('Create user');
    }
}