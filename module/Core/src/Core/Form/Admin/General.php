<?php

namespace Core\Form\Admin;

use Core\Form\AbstractForm as Form;
use Zend\Form\Element;

class General extends Form
{
    public function __construct()
    {
        parent::__construct();
        $zoneArray = [];
        foreach(timezone_identifiers_list() as $zone){
            $zoneArray[$zone] = $zone;
        }
        $this->add(new Element\Select('default_timezone', [
            'value_options' => $zoneArray,
            'label' => 'Default Timezone'
        ]));
    }

}