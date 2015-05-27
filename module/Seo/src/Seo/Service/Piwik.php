<?php
namespace Seo\Service;

use Core\Service\AbstractService;
use Core\Traits\ServiceDoctrine;

class Piwik extends AbstractService
{
    use \Core\Traits\ServiceOption;


    protected static $piwik;


    /*
     * return \VisualAppeal\Piwik
     */
    public function getPiwik()
    {
        if(!self::$piwik){
            self::$piwik = new \VisualAppeal\Piwik(
                $this->getServiceOption()->get('seo', 'piwik_server_url')->getValue(),
                $this->getServiceOption()->get('seo', 'piwik_access_token')->getValue(),
                $this->getServiceOption()->get('seo', 'piwik_site_id')->getValue(),
                \VisualAppeal\Piwik::FORMAT_JSON
            );
        }
        return self::$piwik;
    }

    public function lastWeek()
    {
        $piwik = $this->getPiwik();
        $piwik->reset();
        $piwik->setRange(date('Y-m-d', time() - 604800));
        return $piwik->getVisitsSummary();
    }
}