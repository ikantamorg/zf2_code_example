<?php

namespace Core\Controller;

use Zend\View\Model\ViewModel;

class Index extends Core
{


    public function indexAction()
    {
        $option = $this->getServiceOption()->get('core', 'test');
        $option = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getRepository('Core\Entity\Option')->findOneBy(['id' => 1]);
        return $this->render('core/index');
    }
}
