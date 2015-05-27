<?php

namespace Storage\Form\Admin;

use Admin\Form\AbstractForm as Form;
use Zend\Form\Element;

class Options extends Form
{

    public function __construct()
    {
        parent::__construct();

        $this->add(new \Storage\Form\Element\Storage('default_storage_id', ['label' => 'Default storage']));
    }
}