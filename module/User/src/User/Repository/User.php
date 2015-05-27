<?php
namespace User\Repository;

use Core\Repository\AbstractRepository;

class User extends AbstractRepository
{
    protected function _select(\Doctrine\ORM\QueryBuilder $select, $filters = [], $options = [])
    {
        if(!empty($filters['role_id'])){
            $select->leftJoin('main_table.roles', 'roles');
            $select->where('roles.id = :role_id');
            $select->setParameter('role_id', $filters['role_id']);
        }
    }
}