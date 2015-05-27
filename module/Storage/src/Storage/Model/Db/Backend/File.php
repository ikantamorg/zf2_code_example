<?php
namespace Storage\Model\Db\Backend;

use Core\Model\Db\Backend\AbstractBackend;

class File extends AbstractBackend
{

    protected $_table = "storage_files";



    /* COLLECTION */
    public function getSizeAllByStorageId($storageId)
    {
        $select = $this->select(array('sum(size) as sum'));
        $select->where(array('storage_id'=>$storageId));
        $select->columns(array(new \Zend\Db\Sql\Expression('SUM(size) as size')));

        $sql = $this->getSql();
        $selectString = $sql->getSqlStringForSqlObject($select);

        $adapter = $this->getAdapter();
        $res = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        foreach($res as $s){
            return $s['size'];
        }
    }

    /* PRIVATE */
    protected function paginatorFilter($select, $filter)
    {
        if(!empty($filter['storage_id'])){
            $select->where(['storage_id' => $filter['storage_id']]);
        }

        if(!empty($filter['order_id'])){
            $select->join(['of' => 'checkout_orders_files'], 'of.file_id = storage_files.id', ['type', 'description'])
                ->where(['of.order_id' => $filter['order_id']]);
        }

        if(!empty($filter['mime_minor'])){
            $select->where(['mime_minor' => $filter['mime_minor']]);
        }
        return $select;
    }
}