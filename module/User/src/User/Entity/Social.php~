<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Entity\AbstractEntity;

/**
 * @ORM\Table(name="user_socials")
 * @ORM\Entity(repositoryClass="User\Repository\Social")
 * @ORM\HasLifecycleCallbacks()
 */
class Social extends AbstractEntity{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $identifier;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $provider;

    /** @ORM\Column(type="integer", nullable=false) */
    protected $create_at;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="socials")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

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
     * Set identifier
     *
     * @param string $identifier
     * @return Social
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return Social
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string 
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set create_at
     *
     * @param integer $createAt
     * @return Social
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
     * @return Social
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
}
