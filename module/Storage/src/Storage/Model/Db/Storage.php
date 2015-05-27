<?php
namespace Storage\Model\Db;

use Core\Model\Db\AbstractModel;

class Storage extends AbstractModel
{



    protected $_plugin = null;
    protected $_options = null;


    /* SET */
    public function createFile($dr)
    {
        $path = tempnam(sys_get_temp_dir(), $dr);
        fwrite(fopen($path, 'a'), '');
        $file = $this->createFileTmp($path, time() . '.' . $dr);
        $file->tmp(0);
        return $file;
    }



    /* GET PUBLIC FUNCTIONS */
    public function getSizeFiles($format = null)
    {
        $size = \Storage\Model\Db\Collection\File::getSizeAllByStorageId($this->getId());
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

    public function getPluginTitle()
    {
        $options = $this->getOptions();
        return $options['title'];
    }

    public function getPluginPath()
    {
        return $this->getPlugin()->getPath();
    }

    public function getOptions()
    {
        if(!$this->_options){
            $options = $this->getServiceLocator()->get('config');
            $this->_options = $options['storage_providers'][$this->getPluginIndex()];
        }
        return $this->_options;
    }

    public function getPlugin()
    {
        if(!$this->_plugin){
            $options = $this->getOptions();
            $this->_plugin = new $options['plugin_class'](unserialize($this->getConfig()));
            $this->_plugin->setServiceLocator($this->getServiceLocator());
        }
        return $this->_plugin;
    }

    /* SET PUBLIC FUNCTIONS */
    public function createFileTmp($tmpPath, $name)
    {
        $mime = mime_content_type($tmpPath);
        $mime = explode('/', $mime);
        $file = \Storage\Model\Db\File::createNoSave([
            'mime_major' => $mime[0],
            'mime_minor' => $mime[1],
            'name' => basename($name),
            'size' => filesize($tmpPath),
            'storage_id' => $this->getId()
        ]);

        $file->setPath($this->getPlugin()->create($tmpPath, $file->getExtension()));
        $file->save();
        return $file;
    }

    public function createFilePath($path)
    {
        $mime = mime_content_type($path);
        $mime = explode('/', $mime);
        $file = \Storage\Model\Db\File::createNoSave([
            'mime_major' => $mime[0],
            'mime_minor' => $mime[1],
            'name' => basename($path),
            'size' => filesize($path),
            'storage_id' => $this->getId()
        ]);
        $file->setPath($this->getPlugin()->create($path, $file->getExtension()));
        $file->save();
        return $file;
    }

    public function create($data = array())
    {
        $object = new self();
        $object->setName($data['name'])
            ->setPluginIndex($data['plugin_index'])
            ->setConfig(serialize($data['config']))
            ->save();
        return $object;
    }
}