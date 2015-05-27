<?php

namespace Storage\Form\Admin\AdapterOption;

use Admin\Form\AbstractOption as Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class Local extends Form implements InputFilterProviderInterface
{
    protected $arrayInputFilter = [];


    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add(new Element\Hidden('adapter_class'));
        $this->add(new Element\Text('name', ['label' => 'Name']));
        $this->add(new Element\Text('path', ['label' => 'Path']));

        $this->get('adapter_class')->setValue('Storage\Adapter\Local');
    }

    public function initInputFilter()
    {
        $this->arrayInputFilter = [
            'adapter_class' => ['required' => true],
            'name' => ['required' => true],
            'path' => ['required' => true]
        ];
    }

    public function getInputFilterSpecification()
    {
        return $this->arrayInputFilter;
    }
}