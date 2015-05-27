<?php
namespace Storage\Controller\Admin;

use Admin\Controller\AbstractController;

class Index extends AbstractController
{

    public function indexAction()
    {
        return $this->redirect('admin/storages/manager');
    }
}
