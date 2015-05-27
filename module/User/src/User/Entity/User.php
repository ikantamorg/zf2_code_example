<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Entity\AbstractEntity;

/**
 * @ORM\Table(name="user_users")
 * @ORM\Entity(repositoryClass="User\Repository\User")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends AbstractEntity{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(type="string", nullable=true, length=255) */
    protected $email;

    /** @ORM\Column(type="string", nullable=true, length=255) */
    protected $first_name;

    /** @ORM\Column(type="string", nullable=true, length=255) */
    protected $last_name;

    /** @ORM\Column(type="string", nullable=true, length=255) */
    protected $displayname;

    /** @ORM\Column(type="string", nullable=true) */
    protected $birth_date;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $password;

    /** @ORM\Column(type="integer", nullable=false) */
    protected $create_at;
    
    /** @ORM\Column(type="integer", nullable=true) */
    protected $is_locked;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $active_notification;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $avatar_id;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="user_user_roles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * @ORM\OneToMany(targetEntity="User\Entity\Social", mappedBy="user", cascade={"remove"})
     **/
    protected $socials;

    /**
     * @ORM\OneToMany(targetEntity="User\Entity\Session", mappedBy="user", cascade={"remove"})
     **/
    protected $sessions;


    public function getNameFromAdmin()
    {
        return $this->getEmail();
    }

    public function inRoleSlug($slug)
    {
        foreach($this->getRoles() as $role){
            if($role->getSlug() == $slug){
                return true;
            }
        }
        return false;
    }

    public function inRole(\User\Entity\Role $roles)
    {
        foreach($this->getRoles() as $role){
            if($role->getSlug() == $roles->getSlug()){
                return true;
            }
        }
        return false;
    }

    public function lock()
    {
        $this->setIsLocked(1);
        $this->save();
        return $this;
    }

    public function unlock()
    {
        $this->setIsLocked(0);
        $this->save();
        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->socials = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set displayname
     *
     * @param string $displayname
     *
     * @return User
     */
    public function setDisplayname($displayname)
    {
        $this->displayname = $displayname;

        return $this;
    }

    /**
     * Get displayname
     *
     * @return string
     */
    public function getDisplayname()
    {
        return $this->displayname;
    }

    /**
     * Set birthDate
     *
     * @param string $birthDate
     *
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return string
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set createAt
     *
     * @param integer $createAt
     *
     * @return User
     */
    public function setCreateAt($createAt)
    {
        $this->create_at = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return integer
     */
    public function getCreateAt()
    {
        return $this->create_at;
    }

    /**
     * Set isLocked
     *
     * @param integer $isLocked
     *
     * @return User
     */
    public function setIsLocked($isLocked)
    {
        $this->is_locked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return integer
     */
    public function getIsLocked()
    {
        return $this->is_locked;
    }

    /**
     * Set activeNotification
     *
     * @param integer $activeNotification
     *
     * @return User
     */
    public function setActiveNotification($activeNotification)
    {
        $this->active_notification = $activeNotification;

        return $this;
    }

    /**
     * Get activeNotification
     *
     * @return integer
     */
    public function getActiveNotification()
    {
        return $this->active_notification;
    }

    /**
     * Set avatarId
     *
     * @param integer $avatarId
     *
     * @return User
     */
    public function setAvatarId($avatarId)
    {
        $this->avatar_id = $avatarId;

        return $this;
    }

    /**
     * Get avatarId
     *
     * @return integer
     */
    public function getAvatarId()
    {
        return $this->avatar_id;
    }

    /**
     * Add role
     *
     * @param \User\Entity\Role $role
     *
     * @return User
     */
    public function addRole(\User\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \User\Entity\Role $role
     */
    public function removeRole(\User\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add social
     *
     * @param \User\Entity\Social $social
     *
     * @return User
     */
    public function addSocial(\User\Entity\Social $social)
    {
        $this->socials[] = $social;

        return $this;
    }

    /**
     * Remove social
     *
     * @param \User\Entity\Social $social
     */
    public function removeSocial(\User\Entity\Social $social)
    {
        $this->socials->removeElement($social);
    }

    /**
     * Get socials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSocials()
    {
        return $this->socials;
    }

    /**
     * Add session
     *
     * @param \User\Entity\Session $session
     *
     * @return User
     */
    public function addSession(\User\Entity\Session $session)
    {
        $this->sessions[] = $session;

        return $this;
    }

    /**
     * Remove session
     *
     * @param \User\Entity\Session $session
     */
    public function removeSession(\User\Entity\Session $session)
    {
        $this->sessions->removeElement($session);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSessions()
    {
        return $this->sessions;
    }
}
