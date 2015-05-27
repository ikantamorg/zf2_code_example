<?php
namespace Seo\Traits;

trait ServiceSeo
{
    public function getServiceSeo()
    {
        return $this->getServiceLocator()->get('seo.seo');
    }
}