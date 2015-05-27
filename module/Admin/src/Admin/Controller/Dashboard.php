<?php

namespace Admin\Controller;

class Dashboard extends AbstractController
{
    public function indexAction()
    {
        $widgets = $this->getServiceLocator()->get('config')['admin']['dashboard_widget'];
        return $this->render('admin/dashboard', ['widgets' => $widgets]);
    }
}
