<?php
namespace User\Repository;

use Core\Repository\AbstractRepository;

class Social extends AbstractRepository
{
    protected function _select(\Doctrine\ORM\QueryBuilder $select, $filters = [], $options = [])
    {
        if(!empty($filters['provider'])){
            $select->where('main_table.provider = :provider');
            $select->setParameter('provider', $filters['provider']);
        }

        if(!empty($filters['identifier'])){
            $select->where('main_table.identifier = :identifier');
            $select->setParameter('identifier', $filters['identifier']);
        }
    }
}