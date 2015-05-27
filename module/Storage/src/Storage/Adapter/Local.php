<?php
namespace Storage\Adapter;

class Local extends AbstractAdapter implements InterfaceAdapter
{
    protected $option_form = "Storage\Form\Admin\AdapterOption\Local";
    protected $type = 'local';
    protected $name = "Local Server";
    protected $storage_path = "storage";

    protected $_config;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function getConfigPath()
    {
        return $this->_config['path'];
    }

    public function getPath()
    {
        $path = PUBLIC_PATH . '/' . $this->storage_path . '/' . $this->getConfigPath();
        if( is_dir($path) ) {
            @chmod($path, 0777);
        } else {
            @mkdir($path, 0777, true);
        }
        return $path;
    }


    public function create($path, $extension)
    {
        $newPath = $this->genName($extension);
        $map = $this->map($newPath);

        $dirname = dirname($map);
        if( is_dir($dirname) ) {
            @chmod($dirname, 0777);
        } else {
            @mkdir($dirname, 0777, true);
        }
        copy($path, $map);
        @chmod($map, 0777);
        return $newPath;
    }

    public function remove($path)
    {
        unlink($path);
    }

    public function map($path)
    {

        return $this->getPath() . '/' . $this->storage_path . '/' . $path;
    }

    public function href($path)
    {
        $basePathHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('basePath');
        $serverUrlHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('serverUrl');
        return $serverUrlHelper->getScheme() . '://' . $serverUrlHelper->getHost() . $basePathHelper('') . $this->getConfigPath() . '/' . $path;
    }

    public function copy(\Storage\Model\Db\File $fileFrom, \Storage\Model\Db\File $fileTo)
    {

    }

}