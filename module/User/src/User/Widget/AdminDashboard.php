<?php

namespace User\Widget;

use Zend\View\Model\JsonModel;

class AdminDashboard extends \Core\Widget\AbstractWidget
{
    public function indexAction()
    {
        $countUsers = $this->getServiceDoctrine()
            ->getRepository('User', 'User')
            ->getAllCount();

        return $this->render('widget/user/admin-dashboard', ['count_users' => $countUsers]);
    }
}