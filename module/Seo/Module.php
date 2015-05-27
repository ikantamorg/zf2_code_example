<?php

namespace Seo;

use Core\AbstractModule;
use Seo\Service\Seo;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    use \Core\Traits\ServiceOption;
    use \Core\Traits\ServiceTraits;

    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;


    public function onBootstrap(MvcEvent $e)
    {
        $em = $e->getApplication()->getEventManager();

        $em->attach(\Zend\Mvc\MvcEvent::EVENT_ROUTE,function($ev) use ($e){
            $routs = explode('/', $ev->getRouteMatch()->getMatchedRouteName());
            if($routs[0] !== 'admin'){
                $url = $this->getServiceOption()->get('seo', 'piwik_server_url')->getValue();
                $siteId = $this->getServiceOption()->get('seo', 'piwik_site_id')->getValue();

                $headScript = $e->getApplication()->getServiceManager()->get('viewhelpermanager')->get('headScript');

                $script = "var _paq =_paq || [];_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);
                (function(){var u=\"" . $url . "/\";_paq.push(['setTrackerUrl',u+'piwik.php']);_paq.push(['setSiteId'," . $siteId . "]);
                var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];g.type='text/javascript';
                g.async=true;g.defer=true;g.src=u+'piwik.js';s.parentNode.insertBefore(g,s);})()";

                $headScript->appendScript($script);
            }

        });
        parent::onBootstrap($e);
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'seo.seo' => function($serviceManager){
                    return new Seo($serviceManager);
                },
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'renderTitle' => function($serviceManager) {
                    return new \Seo\View\Helper\RenderTitle($serviceManager->getServiceLocator());
                },
                'renderDescription' => function($serviceManager) {
                    return new \Seo\View\Helper\RenderDescription($serviceManager->getServiceLocator());
                },
                'renderKeywords' => function($serviceManager) {
                    return new \Seo\View\Helper\RenderKeywords($serviceManager->getServiceLocator());
                },
                'renderMeta' => function($serviceManager) {
                    return new \Seo\View\Helper\RenderMeta($serviceManager->getServiceLocator());
                },
                'renderHead' => function($serviceManager) {
                    return new \Seo\View\Helper\RenderHead($serviceManager->getServiceLocator());
                }
            ),
        );
    }
}
