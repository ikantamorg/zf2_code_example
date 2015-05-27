<?php

namespace Storage\Form\Admin\AdapterOption;

use Zend\Form\Element;
use Admin\Form\AbstractOption as Form;
use Zend\InputFilter\InputFilterProviderInterface;

class S3 extends Form implements InputFilterProviderInterface
{
    protected $arrayInputFilter = [];


    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add(new Element\Hidden('adapter_class'));
        $this->add(new Element\Text('name', ['label' => 'Name']));
        $this->add(new Element\Text('path', ['label' => 'Path']));
        $this->add(new Element\Text('access_key', ['label' => 'Access Key']));
        $this->add(new Element\Text('secret_key', ['label' => 'Secret Key']));
        $this->add(new Element\Text('bucket', ['label' => 'Bucket']));

        $this->get('adapter_class')->setValue('Storage\Adapter\S3');
    }

    public function initInputFilter()
    {
        $this->arrayInputFilter = [
            'adapter_class' => ['required' => true],
            'name' => ['required' => true],
            'path' => ['required' => true],
            'access_key' => ['required' => true],
            'secret_key' => ['required' => true],
            'bucket' => ['required' => true]
        ];
    }

    public function getInputFilterSpecification()
    {
        return $this->arrayInputFilter;
    }
}