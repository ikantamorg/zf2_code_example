<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Entity\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="Storage\Repository\File")
 * @ORM\Table(name="storage_files")
 */
class File extends AbstractEntity{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Storage\Entity\Storage", inversedBy="files")
     * @ORM\JoinColumn(name="storage_id", referencedColumnName="id")
     **/
    protected $storage;

    /** @ORM\Column(type="integer", nullable=false, length=64) */
    protected $user_id;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $name;

    /** @ORM\Column(type="string", nullable=false, length=1024) */
    protected $path;

    /** @ORM\Column(type="integer", nullable=false, length=1024) */
    protected $size;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $mime_major;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $mime_minor;

    /** @ORM\Column(type="integer", nullable=true, length=1) */
    protected $is_tmp;

    /** @ORM\Column(type="integer", nullable=true, length=64) */
    protected $create_at;

    /** @ORM\Column(type="integer", nullable=true, length=64) */
    protected $modified_at;

    public function createClone()
    {
        $extension = explode('.', $this->getName());
        $file = $this->getStorage()->upload($this->getMap(), end($extension));
        return $file;
    }

    public function getHref()
    {
        return $this->getPath() ? $this->getStorage()->getAdapter()->href($this->getPath()) : null;
    }

    public function getMap()
    {
        return $this->getStorage()->getAdapter()->map($this->getPath());
    }

    public function unsetId()
    {
        $this->id = null;
        return $this;
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
     * Set user_id
     *
     * @param integer $userId
     * @return File
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set mime_major
     *
     * @param string $mimeMajor
     * @return File
     */
    public function setMimeMajor($mimeMajor)
    {
        $this->mime_major = $mimeMajor;

        return $this;
    }

    /**
     * Get mime_major
     *
     * @return string 
     */
    public function getMimeMajor()
    {
        return $this->mime_major;
    }

    /**
     * Set mime_minor
     *
     * @param string $mimeMinor
     * @return File
     */
    public function setMimeMinor($mimeMinor)
    {
        $this->mime_minor = $mimeMinor;

        return $this;
    }

    /**
     * Get mime_minor
     *
     * @return string 
     */
    public function getMimeMinor()
    {
        return $this->mime_minor;
    }

    /**
     * Set is_tmp
     *
     * @param integer $isTmp
     * @return File
     */
    public function setIsTmp($isTmp)
    {
        $this->is_tmp = $isTmp;

        return $this;
    }

    /**
     * Get is_tmp
     *
     * @return integer 
     */
    public function getIsTmp()
    {
        return $this->is_tmp;
    }

    /**
     * Set create_at
     *
     * @param integer $createAt
     * @return File
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
     * Set modified_at
     *
     * @param integer $modifiedAt
     * @return File
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modified_at = $modifiedAt;

        return $this;
    }

    /**
     * Get modified_at
     *
     * @return integer 
     */
    public function getModifiedAt()
    {
        return $this->modified_at;
    }

    /**
     * Set storage
     *
     * @param \Storage\Entity\Storage $storage
     * @return File
     */
    public function setStorage(\Storage\Entity\Storage $storage = null)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Get storage
     *
     * @return \Storage\Entity\Storage 
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
