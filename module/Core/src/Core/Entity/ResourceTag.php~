<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\Entity(repositoryClass="Core\Repository\ResourceTag")
 * @ORM\Table(name="core_resource_tags")
 */
class ResourceTag extends AbstractEntity{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $tag;

    /**
     * @ORM\ManyToMany(targetEntity="Core\Entity\ResourceObject", mappedBy="tags")
     */
    protected $objects;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objects = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tag
     *
     * @param string $tag
     * @return ResourceTag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Add objects
     *
     * @param \Core\Entity\ResourceObject $objects
     * @return ResourceTag
     */
    public function addObject(\Core\Entity\ResourceObject $objects)
    {
        $this->objects[] = $objects;

        return $this;
    }

    /**
     * Remove objects
     *
     * @param \Core\Entity\ResourceObject $objects
     */
    public function removeObject(\Core\Entity\ResourceObject $objects)
    {
        $this->objects->removeElement($objects);
    }

    /**
     * Get objects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObjects()
    {
        return $this->objects;
    }
}
