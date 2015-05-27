<?php

namespace Core\View\Helper;

use Zend\View\Helper\Partial;

class PaginationHelper extends Partial
{
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke(\Doctrine\ORM\Tools\Pagination\Paginator $collection, $options = [])
    {
        $countAll = $collection->count();

        $offset = $collection->getQuery()->getFirstResult();
        $countToPage = $collection->getQuery()->getMaxResults();

        $activePage = ($offset / $countToPage) + 1;
        $countPages = ceil($countAll / $countToPage);

        $options['countAll'] = $countAll;
        $options['start'] = $countAll ? ($activePage - 1) * $countToPage + 1 : 0;
        $options['end'] = $countAll ? ($activePage) * $countToPage : 0;

        if($activePage == $countPages){
            $options['end'] = $countAll;
        }

        $options['collection'] = $collection;
        $options['countPages'] = $countPages;
        $options['activePage'] = $activePage;

        return parent::__invoke(empty($options['template']) ? 'helper/core/pagination' : $options['template'], $options);
    }
}