<?php
namespace Storage\Service;

use Core\Service\AbstractService;
use Core\Traits\ServiceOption;
use Core\Traits\ServiceDoctrine;

class Storage extends AbstractService
{
    use ServiceOption;
    use ServiceDoctrine;


    public function get($id)
    {
        $storage = $this->getServiceDoctrine()->getEntity('Storage', 'Storage', $id);
        if(!$storage){
            $storage = $this->getServiceDoctrine()->getEntity('Storage', 'Storage', $this->getServiceOption()->get('storage', 'default_storage_id')->getValue());
        }
        return $storage;
    }

    public function getDefault()
    {
        $defaultStorageId = $this->getServiceOption()->get('storage', 'default_storage_id')->getValue();
        return $this->get($defaultStorageId);
    }

    public function createStorage($data)
    {
        $storage = new \Storage\Entity\Storage();
        $storage->setName($data['name']);
        $storage->setAdapterClass($data['adapter_class']);
        $storage->setConfig(serialize($data['config']));
        $storage->save();
        return $storage;
    }
}