<?php

namespace Seo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Seo\Traits\ServiceSeo;
use Core\Traits\ServiceOption;

class RenderMeta extends AbstractHelper
{
    use ServiceSeo;
    use ServiceOption;


    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke()
    {
        $title = $this->getServiceOption()->get('seo', 'title')->getValue();
        $meta = '';

        $meta .= '<meta name="title" content="' . $this->getServiceSeo()->getTitleString() . '">
    <meta name="twitter:title" content="' . $this->getServiceSeo()->getTitleString() . '">
    <meta property="og:title" content="' . $this->getServiceSeo()->getTitleString() . '" />
    <meta property="og:site_name" content="' . $title . '" />
    <meta itemprop="name" content="' . $title . '">
    <meta name="keywords" content="' . $this->getServiceSeo()->getKeywordsString() . '" />
    <meta name="twitter:description" content="' . $this->getServiceSeo()->getDescriptionString() . '">
    <meta property="og:description" content="' . $this->getServiceSeo()->getDescriptionString() . '" />
    <meta itemprop="description" content="' . $this->getServiceSeo()->getDescriptionString() . '">
    <meta name="description" content="' . $this->getServiceSeo()->getDescriptionString() . '">
    ';

        $image = $this->getServiceSeo()->getImageHref();
        if(!empty($image)){
            $meta .= '<meta itemprop="image" content="' . $image . '">
    <meta name="twitter:image" content="' . $image . '">
    <meta property="og:image" content="' . $image . '"/>
    <link rel="conforming" href="' . $image . '" rel="image_src"/>
    <meta name="twitter:image:src" content="' . $image . '"/>
            ';
        }
        return $meta;
    }
} 