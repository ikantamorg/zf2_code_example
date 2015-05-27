<?php
namespace Core\Repository;

class ResourceView extends AbstractRepository {

    public function getCountByResourceId($objectId)
    {
        return $this->getAllCount(['object_id' => $objectId]);
    }

    protected function _select(\Doctrine\ORM\QueryBuilder $select, $filters = [], $options = [])
    {
        if(!empty($filters['object_id'])){
            $select->leftJoin('main_table.object', 'object');
            $select->where('object.id = :object_id');
            $select->setParameter('object_id', $filters['object_id']);
        }
    }
}