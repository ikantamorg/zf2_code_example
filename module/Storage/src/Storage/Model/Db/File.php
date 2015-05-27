<?php
namespace Storage\Model\Db;

use Core\Model\Db\AbstractModel;
use User\Traits\ServiceUser;
use Core\Traits\ServiceOption;

class File extends AbstractModel
{
    use ServiceOption;
    use ServiceUser;

    protected $_storageModel = null;
    protected $_userModel = null;


    /* SET PUBLIC FUNCTIONS */
    public static function createNoSave($data)
    {
        $object = new self();

        $object
            ->setMimeMajor($data['mime_major'])
            ->setMimeMinor($data['mime_minor'])
            ->setName($data['name'])
            ->setSize($data['size'])
            ->setCreateAt(time())
            ->setIsTmp(1)
            ->setStorageId($data['storage_id'])
            ->setUserId($object->getServiceUser()->getLogin()->getId());

        return $object;
    }

    public function tmp($flag)
    {
        $this->setIsTmp($flag)->save();
        return $this;
    }

    public function delete()
    {
        $this->getPlugin()->remove($this->getMap());
        return parent::delete();
    }


    /* GET */
    public function getExtension()
    {
        $extension = explode('.', $this->getName());
        return end($extension);
    }

    public function getMap()
    {
        return $this->getPlugin()->map($this->getPath());
    }

    public function getHref()
    {
        return $this->getPath() ? $this->getPlugin()->href($this->getPath()) : null;
    }

    public function getMime()
    {
        return $this->getMimeMajor() . '/' . $this->getMimeMinor();
    }

    public function getStorageName()
    {
        return $this->getStorage()->getName();
    }

    public function getSize($format = null)
    {
        $size = parent::getSize();
        switch($format){
            case 'KB':
                return number_format(parent::getSize()/1024, 2);
                break;
            case 'MB':
                return number_format((parent::getSize()/1024)/1024, 2);
                break;
            default:
                return $size;
                break;
        }
    }

    public function getPlugin()
    {
        return $this->getStorage()->getPlugin();
    }

    public function getUserFullName()
    {
        return $this->getUser()->getFullName();
    }

    public function getDownloadResponse()
    {
        $filePath = $this->getMap();

        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($filePath, 'r'));
        $response->setStatusCode(200);

        $headers = new \Zend\Http\Headers();
        $headers->addHeaderLine('Content-Type', $this->getMime())
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $this->getName() . '"')
            ->addHeaderLine('Content-Length', $this->getSize());

        $response->setHeaders($headers);

        return $response;
    }

    /* LINK MODEL */
    public function getStorage()
    {
        if(!$this->_storageModel){
            $this->_storageModel = new \Storage\Model\Db\Storage($this->getStorageId());
            if(!$this->_storageModel->getId()){
                $this->_storageModel->findById($this->getOption()->get('storage', 'default_storage_id'));
            }
        }
        return $this->_storageModel;
    }

    public function getUser()
    {
        if(!$this->_userModel){
            $this->_userModel = new \User\Model\Db\User($this->getUserId());
        }
        return $this->_userModel;
    }

    public function createClone()
    {
        $clone = clone $this;
        $clone->unsId();
        $clone->setPath($this->getPlugin()->create($this->getMap(), $this->getExtension()));
        $clone->save();
        return $clone;
    }

    /* PRIVATE FUNCTIONS */
    protected function beforeSave()
    {
        $this->setModifiedAt(time());
        return $this;
    }

    protected function beforeUpdate()
    {
        $this->setSize(filesize($this->getMap()));
        return $this;
    }
}