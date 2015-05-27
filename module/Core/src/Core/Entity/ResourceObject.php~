<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Traits\ServiceResource;

/**
 * @ORM\Entity
 * @ORM\Table(name="core_resource_objects")
 */
class ResourceObject extends AbstractEntity{

    use ServiceResource;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Core\Entity\Resource", inversedBy="views")
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     **/
    protected $resource;

    /** @ORM\Column(type="integer") */
    protected $object_id;

    /**
     * @ORM\OneToMany(targetEntity="Core\Entity\ResourceView", mappedBy="object", cascade={"persist", "remove"})
     **/
    protected $views;

    /**
     * @ORM\ManyToMany(targetEntity="Core\Entity\ResourceTag", inversedBy="object")
     * @ORM\JoinTable(name="core_resource_object_tags",
     *      joinColumns={@ORM\JoinColumn(name="object_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    protected $tags;

    public function getViewsCount()
    {
        return $this->getServiceDoctrine()->getRepository('Core', 'ResourceView')->getAllCount([
            'object_id' => $this->getId()
        ]);
    }

    public function clearTags()
    {
        $tags = $this->getTags();
        if(count($tags)){
            $this->getTags()->clear();
        }
    }

    public function getTagsString($sep = ',')
    {
        $arrayTags = $this->getTagsArray();
        return implode($sep, $arrayTags);
    }

    public function getTagsArray()
    {
        $arrayTags = [];
        if($this->getTags())
            foreach($this->getTags() as $tag){
                $arrayTags[] = $tag->getTag();
            }

        return $arrayTags;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->views = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set object_id
     *
     * @param integer $objectId
     * @return ResourceObject
     */
    public function setObjectId($objectId)
    {
        $this->object_id = $objectId;

        return $this;
    }

    /**
     * Get object_id
     *
     * @return integer 
     */
    public function getObjectId()
    {
        return $this->object_id;
    }

    /**
     * Set resource
     *
     * @param \Core\Entity\Resource $resource
     * @return ResourceObject
     */
    public function setResource(\Core\Entity\Resource $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \Core\Entity\Resource 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Add views
     *
     * @param \Core\Entity\ResourceView $views
     * @return ResourceObject
     */
    public function addView(\Core\Entity\ResourceView $views)
    {
        $this->views[] = $views;

        return $this;
    }

    /**
     * Remove views
     *
     * @param \Core\Entity\ResourceView $views
     */
    public function removeView(\Core\Entity\ResourceView $views)
    {
        $this->views->removeElement($views);
    }

    /**
     * Get views
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add tags
     *
     * @param \Core\Entity\ResourceTag $tags
     * @return ResourceObject
     */
    public function addTag(\Core\Entity\ResourceTag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Core\Entity\ResourceTag $tags
     */
    public function removeTag(\Core\Entity\ResourceTag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
}
