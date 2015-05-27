<?php

namespace Seo\Widget;

use Zend\View\Model\JsonModel;

class AdminDashboardPiwik extends \Core\Widget\AbstractWidget
{
    public function indexAction()
    {
        $piwik = $this->getServiceLocator()->get('piwik');
   // print_r($piwik->lastWeek()); exit;
        return $this->render('widget/seo/admin-dashboard-piwik', [
            'lastWeek' => $piwik->lastWeek()
        ]);
    }
}