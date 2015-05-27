<?php
namespace Oriflame\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Blog\Traits\ServiceBlog;
use Core\Traits\ServiceTraits;

class Core extends AbstractFixture implements OrderedFixtureInterface
{
    use ServiceBlog;
    use ServiceTraits;

    public function load(ObjectManager $manager)
    {
        $option = new \Core\Entity\Option();
        $option->setModule('core');
        $option->setName('default_timezone');
        $option->setValue('UTC');
        $option->save();
    }

    public function getOrder()
    {
        return 1;
    }
}