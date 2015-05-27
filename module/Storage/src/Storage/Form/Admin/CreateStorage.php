<?php

namespace Storage\Form\Admin;

use Admin\Form\AbstractForm as Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;

class CreateStorage extends Form
{
    protected $formsArrayClass = [];


    public function __construct()
    {
        parent::__construct();

        $this->setOption('use_as_base_fieldset', true);

        foreach(glob(dirname(__FILE__).'./../../Adapter/*.php') as $class_path) {
            require_once($class_path);
        }

        $this->setLabel('Create Storage');
        $arrayTypes = [];

        foreach (get_declared_classes() as $class) {
            if (get_parent_class($class) == 'Storage\Adapter\AbstractAdapter') {
                $adapter = new $class([]);
                $arrayTypes[$adapter->getType()] = $adapter->getName();
            }
        }

        $this->add(new Element\Select('type', [
            'label' => 'type',
            'options' => $arrayTypes
        ]));

        foreach (get_declared_classes() as $class) {
            if (get_parent_class($class) == 'Storage\Adapter\AbstractAdapter') {
                $adapter = new $class([]);
                $form = $adapter->getFormOption($adapter->getType());
                $this->add($form);
            }
        }
    }

    public function setData($data)
    {
        $this->get($data['type'])->initInputFilter();
        return parent::setData($data);
    }

    public function getData($flag = \Zend\Form\FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);
        $config = $data[$data['type']];
        $data['adapter_class'] = $config['adapter_class'];
        $data['name'] = $config['name'];
        $data['config'] = $config;
        return $data;
    }
}