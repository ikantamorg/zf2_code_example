<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Entity\AbstractEntity;

/**
 * @ORM\Table(name="user_sessions")
 * @ORM\Entity(repositoryClass="User\Repository\Session")
 * @ORM\HasLifecycleCallbacks()
 */
class Session extends AbstractEntity{
    use \Core\Traits\ServiceSession;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $identification;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $browser;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $platform;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $ip;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $version;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $aol_version;

    /** @ORM\Column(type="integer", nullable=false) */
    protected $create_at;

    /** @ORM\Column(type="integer", nullable=false) */
    protected $last_activity;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $is_login;
    
    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="sessions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

    public function isNow()
    {
        return $this->getIdentification() == $this->getServiceSession()->get()->getManager()->getId();
    }

    public function getLastAgo()
    {
        return $this->timeAgo($this->getLastActivity());
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
     * Set identification
     *
     * @param string $identification
     * @return Session
     */
    public function setIdentification($identification)
    {
        $this->identification = $identification;

        return $this;
    }

    /**
     * Get identification
     *
     * @return string 
     */
    public function getIdentification()
    {
        return $this->identification;
    }

    /**
     * Set create_at
     *
     * @param integer $createAt
     * @return Session
     */
    public function setCreateAt($createAt)
    {
        $this->create_at = $createAt;

        return $this;
    }

    /**
     * Get create_at
     *
     * @return integer 
     */
    public function getCreateAt()
    {
        return $this->create_at;
    }

    /**
     * Set user
     *
     * @param \User\Entity\User $user
     * @return Session
     */
    public function setUser(\User\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \User\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set last_activity
     *
     * @param integer $lastActivity
     * @return Session
     */
    public function setLastActivity($lastActivity)
    {
        $this->last_activity = $lastActivity;

        return $this;
    }

    /**
     * Get last_activity
     *
     * @return integer 
     */
    public function getLastActivity()
    {
        return $this->last_activity;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Session
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set browser
     *
     * @param string $browser
     * @return Session
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * Get browser
     *
     * @return string 
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Set platform
     *
     * @param string $platform
     * @return Session
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string 
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return Session
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set aol_version
     *
     * @param string $aolVersion
     * @return Session
     */
    public function setAolVersion($aolVersion)
    {
        $this->aol_version = $aolVersion;

        return $this;
    }

    /**
     * Get aol_version
     *
     * @return string 
     */
    public function getAolVersion()
    {
        return $this->aol_version;
    }

    /**
     * Set is_login
     *
     * @param integer $isLogin
     * @return Session
     */
    public function setIsLogin($isLogin)
    {
        $this->is_login = $isLogin;

        return $this;
    }

    /**
     * Get is_login
     *
     * @return integer 
     */
    public function getIsLogin()
    {
        return $this->is_login;
    }
}
