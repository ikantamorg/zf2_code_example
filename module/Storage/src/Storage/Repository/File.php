<?php
namespace Storage\Repository;

use Core\Repository\AbstractRepository;

class File extends AbstractRepository
{
    public function getSizeFilesByStorageId($storageId)
    {
        $em = $this->getEntityManager();

        $queryAvgScore = $em->createQueryBuilder()
            ->select(array('total_size' => 'SUM(main_table.size)'))
            ->from($this->getEntityName(), 'main_table')
            ->leftJoin('main_table.storage', 'storage')
            ->where('storage.id = :storage_id')
            ->setParameter('storage_id', $storageId)
            ->getQuery();
        return $queryAvgScore->getResult()[0][1];
    }
}