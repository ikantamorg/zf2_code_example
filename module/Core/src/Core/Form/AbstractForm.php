<?php

namespace Core\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Core\Traits\ServiceTraits;

class AbstractForm extends Form
{
    use ServiceTraits;


    public function __construct($name = null, $options = array())
    {
        return parent::__construct($name, $options);
    }
}