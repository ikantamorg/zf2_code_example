<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\Entity(repositoryClass="Core\Repository\ResourceView")
 * @ORM\Table(name="core_resource_views")
 */
class ResourceView extends AbstractEntity{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(type="integer", nullable=true, length=64) */
    protected $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Entity\ResourceObject", inversedBy="views")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     **/
    protected $object;

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
     * Set created_at
     *
     * @param integer $createdAt
     * @return ResourceView
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return integer 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set object
     *
     * @param \Core\Entity\ResourceObject $object
     * @return ResourceView
     */
    public function setObject(\Core\Entity\ResourceObject $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return \Core\Entity\ResourceObject 
     */
    public function getObject()
    {
        return $this->object;
    }
}
