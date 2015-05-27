<?php
namespace Storage\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Storage\Model\Db\Storage;

class Role extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $storage = new \Storage\Entity\Storage();
        $storage->setName('Main');
        $storage->setAdapterClass("Storage\Adapter\Local");
        $storage->setConfig('a:1:{s:4:"path";s:4:"main";}');
        $storage->save();

        $option = new \Core\Entity\Option();
        $option->setModule('storage');
        $option->setName('default_storage_id');
        $option->setValue($storage->getId());
        $option->save();
    }

    public function getOrder()
    {
        return 1;
    }
}