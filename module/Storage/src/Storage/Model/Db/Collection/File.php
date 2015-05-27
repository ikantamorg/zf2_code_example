<?php
namespace Storage\Model\Db\Collection;

use Core\Model\Db\Collection\AbstractCollection;

class File extends AbstractCollection
{

    public static function getSizeAllByStorageId($storageId)
    {
        $object = new self();
        return $object->getBackend()->getSizeAllByStorageId($storageId);
    }

}