<?php
namespace Core\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AbstractRepository extends EntityRepository
{
    protected $_countToPage = 10;



    public function getSelect($filters = [], $options = [])
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $select = $qb->select('main_table')->from($this->getEntityName(), 'main_table');

        if(!empty($options['count_to_page']) && $countToPage = round($options['count_to_page'])){
            $this->_countToPage = $countToPage;
        }

        $page = !empty($options['page']) ? round($options['page']) : 1;

        $select->setMaxResults($this->_countToPage);
        $select->setFirstResult(($page - 1) * $this->_countToPage);

        $this->_select($select, $filters, $options);

        return $select->getQuery();
    }

    public function setCountToPage($page)
    {
        $this->_countToPage = $page;
        return $this;
    }

    public function getPage($filters = [], $options = [])
    {
        $list = new Paginator($this->getSelect($filters, $options));
        return $list;
    }

    protected function _select(\Doctrine\ORM\QueryBuilder $select, $filters = [], $options = []) {}

    public function getAllCount($filters = [], $options = [])
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $select = $qb->select('main_table')->from($this->getEntityName(), 'main_table');
        $this->_select($select, $filters, $options);

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb->getQuery());

        return count($paginator);
    }
}