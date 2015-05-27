<?php

namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Entity\AbstractEntity;
use User\Traits\ServiceAuth;
use Core\Traits\ServiceTraits;
use Core\Traits\ServiceDoctrine;

/**
 * @ORM\Entity(repositoryClass="Storage\Repository\Storage")
 * @ORM\Table(name="storage_storages")
 */
class Storage extends AbstractEntity
{
    use ServiceTraits;
    use ServiceDoctrine;
    use ServiceAuth;


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Storage\Entity\File", mappedBy="storage", cascade={"remove"})
     **/
    protected $files;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $name;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $adapter_class;

    /** @ORM\Column(type="string", nullable=false, length=255) */
    protected $config;

    public function getSizeFiles($format = null)
    {
        $size = $this->getServiceDoctrine()->getRepository('Storage', 'File')->getSizeFilesByStorageId($this->getId());
        $size = $size ? $size : 0;
        switch($format){
            case 'KB':
                return number_format($size/1024, 2);
                break;
            case 'MB':
                return number_format(($size/1024)/1024, 2);
                break;
            default:
                return $size;
                break;
        }
    }

    public function getAdapter()
    {
        $class = $this->getAdapterClass();
        return new $class(unserialize($this->getConfig()));
    }

    public function upload($pathFile, $name = null)
    {

        $mime = mime_content_type($pathFile);
        $mime = explode('/', $mime);
        $name = $name ? $name : basename($pathFile);
        $extension = explode('.', $name);


        $path = $this->getAdapter()->create($pathFile, end($extension));

        $file = new \Storage\Entity\File();

        if($this->getServiceAuth()->isLogin()){
            $file->setUserId($this->getServiceAuth()->getLoginUser()->getId());
        } else {
            $file->setUserId(null);
        }

        $file->setMimeMajor($mime[0]);
        $file->setMimeMinor($mime[1]);
        $file->setName($name);
        $file->setSize(filesize($pathFile));
        $file->setStorage($this);
        $file->setCreateAt(time());
        $file->setModifiedAt(time());
        $file->setIsTmp(1);
        $file->setPath($path);
        $file->save();
        return $file;
    }

    public function uploadData($data, $name)
    {
        $extension = explode('.', $name);
        $extension = end($extension);

        $path = tempnam(sys_get_temp_dir(), $extension);
        fwrite(fopen($path, 'a'), $data);
        return $this->upload($path, $name);
    }

    public function uploadUrlContent($url)
    {
        $name = explode('/', $url);
        $name = end($name);

        $data = file_get_contents($url);
        return $this->uploadData($data, $name);
    }

    public function createFile($dr)
    {
        $path = tempnam(sys_get_temp_dir(), $dr);
        fwrite(fopen($path, 'a'), '');
        $file = $this->createFileTmp($path, time() . '.' . $dr);
        $file->tmp(0);
        return $file;
    }

    public function getTypeName()
    {
        return $this->getAdapter()->getName();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Storage
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
     * Set adapter_class
     *
     * @param string $adapterClass
     * @return Storage
     */
    public function setAdapterClass($adapterClass)
    {
        $this->adapter_class = $adapterClass;

        return $this;
    }

    /**
     * Get adapter_class
     *
     * @return string 
     */
    public function getAdapterClass()
    {
        return $this->adapter_class;
    }

    /**
     * Set config
     *
     * @param string $config
     * @return Storage
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return string 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Add files
     *
     * @param \Storage\Entity\File $files
     * @return Storage
     */
    public function addFile(\Storage\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \Storage\Entity\File $files
     */
    public function removeFile(\Storage\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
}
