<?php

namespace User\Form\Admin;

use Zend\Form\Element;
use Core\Form\AbstractForm;


class CreateRole extends AbstractForm
{
    use \Core\Traits\ServiceDoctrine;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setLabel('Create role');

        $this->add(new Element\Text('name'));
    }
}