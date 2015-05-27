<?php
namespace User\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class Role extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $role = new \User\Entity\Role();
        $role->setSlug('admin');
        $role->setName('Admin');
        $role->save();

        $role = new \User\Entity\Role();
        $role->setSlug('member');
        $role->setName('Member');
        $role->save();
    }

    public function getOrder()
    {
        return 1;
    }
}