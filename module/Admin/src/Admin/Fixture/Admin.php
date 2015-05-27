<?php
namespace Admin\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Core\Traits\ServiceTraits;
use Core\Traits\ServiceDoctrine;

class Admin extends AbstractFixture implements OrderedFixtureInterface
{
    use ServiceTraits;
    use ServiceDoctrine;


    public function load(ObjectManager $manager)
    {
        $user = new \User\Entity\User();
        $user->setEmail('admin@admin.com');
        $user->setPassword('$2y$10$nAUZSBbNjizvpXDr5.hx6OR9EMMI9Dw9lIWV4PVwRXqdQADIHSlLi');
        $user->setCreateAt(time());
        $user->setLastActivity(time());
        
        $role = $this->getServiceDoctrine()->getRepository('User', 'Role')->findOneBy(['slug' => 'admin']);
        $user->addRole($role);
        $user->save();
    }

    public function getOrder()
    {
        return 2;
    }
}