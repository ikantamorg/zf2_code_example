<?php
namespace User\Service;

use Core\Service\AbstractService;
use Core\Traits\ServiceDoctrine;
use Zend\Crypt\Password\Bcrypt;

class User extends AbstractService
{
    const MAIN_ROLE = 'member';
    use ServiceDoctrine;
    use \Storage\Traits\ServiceStorage;
    use \Core\Traits\ServiceOption;


    protected static $_storage;

    public function createUser($data)
    {
        $slug = empty($data['role_slug']) ? self::MAIN_ROLE : $data['role_slug'];
        $data['displayname'] = empty($data['displayname']) ? $data['displayname'] : explode('@', $data['email']);

        $user = new \User\Entity\User();
        $user->setActiveNotification(1);
        $user->setCreateAt(time());
        $user->setIsLocked(0);

        $user->setPassword($this->_genPassword($data['password']));
        $user->setDisplayname($data['displayname']);
        $user->setEmail($data['email']);

        if(!empty($data['last_name']))
            $user->setLastName($data['last_name']);

        if(!empty($data['first_name']))
            $user->setLastName($data['first_name']);

        if(!empty($data['birth_date']))
            $user->setLastName($data['birth_date']);

        if(is_array($slug)){
            foreach($slug as $_slug){
                $user->addRole($this->getRoleBySlug($_slug));
            }
        } else {
            $user->addRole($this->getRoleBySlug($slug));
        }

        $user->save();
        return $user;
    }

    public function createAvatar($data)
    {
        if(!empty($data['url'])){
            $bigFile = $this->getStorage()->uploadUrlContent($data['url']);
        } else if(!empty($data['tmp_name']) && !empty($data['name'])){
            $bigFile = $this->getStorage()->upload($data['tmp_name'], $data['name']);
        } else {
            return null;
        }

        $smallFile = $bigFile->createClone();
        $smallImage = new \Storage\Type\Image($smallFile);
        $smallImage->cropAndResize(60, 60);

        $middleFile = $bigFile->createClone();
        $middleImage = new \Storage\Type\Image($middleFile);
        $middleImage->cropAndResize(200, 200);

        $bigImage = new \Storage\Type\Image($bigFile);
        $bigImage->outResize(800, 600);

        $image = new \User\Entity\Avatar();
        $image->setBigFile($bigFile);
        $image->setMiddleFile($middleFile);
        $image->setSmallFile($smallFile);
        $image->setIsTmp(1);
        $image->setCreateAt(time());
        $image->save();

        return $image;
    }

    public function createRole($data)
    {
        $slug = $this->getServiceLocator()->get('Seo\Slug');

        $role = new \User\Entity\Role();
        $role->setName($data['name']);
        $role->setSlug($slug->create($data['name']));
        $role->save();
        return $role;
    }

    public function editUser(\User\Entity\User $userEntity, $data)
    {
        $userEntity->setEmail($data['email']);
        $userEntity->save();
        return $userEntity;
    }

    /* PRIVATE */
    private function _genPassword($password)
    {
        $bcrypt = new Bcrypt();
        return $bcrypt->create($password);
    }

    public function getRoleBySlug($slug)
    {
        return $this->getServiceDoctrine()->getRepository('User', 'Role')->findOneBy(['slug' => $slug]);
    }

    public function getStorage()
    {
        if(!self::$_storage){
            self::$_storage = $this->getServiceStorage()->get($this->getServiceOption()->get('user', 'default_storage_id')->getValue());
        }
        return self::$_storage;
    }
}