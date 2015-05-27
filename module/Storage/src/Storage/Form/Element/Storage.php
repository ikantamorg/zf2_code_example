<?php
namespace Storage\Form\Element;

use Zend\Form\Element;
use Core\Traits\ServiceDoctrine;
use Core\Traits\ServiceTraits;

class Storage extends Element\Select
{
    use ServiceDoctrine;
    use ServiceTraits;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $storageCollection = $this->getServiceDoctrine()->getRepository('Storage', 'Storage')->findAll();

        $storages = [];
        foreach($storageCollection as $storage){
            $storages[$storage->getId()] = $storage->getName();
        }
        $this->setValueOptions($storages);
    }
}