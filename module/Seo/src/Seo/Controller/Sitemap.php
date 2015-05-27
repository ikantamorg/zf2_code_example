<?php
namespace Seo\Controller;

use Core\Controller\Core;
use Seo\Traits\ServiceSeo;
use XMLWriter;

class Sitemap extends Core
{
    use ServiceSeo;


    public function indexAction()
    {
        $ns = 'http://www.sitemaps.org/schemas/sitemap/0.9';

        $writer= new XmlWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->writeRaw( '<foo><bar>Baz</bar></foo>');
        $writer->endDocument();


        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $urlset = $dom->createElementNS($ns, 'urlset');
        $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        $this->getEventManager()->trigger('seo.get-sitemap', ['dom' => $dom, 'urlset' => $urlset]);

        foreach($this->getServiceSeo()->getSitemapElements() as $element){

            if(!empty($element['loc'])){
                $date = !empty($element['lastmod']) ? $element['lastmod'] : date('Y-m-d', time());

                $url = $dom->createElementNS($ns, 'url');
                $url->appendChild($dom->createElementNS($ns,'loc', $element['loc']));
                $url->appendChild($dom->createElementNS($ns,'lastmod', $date));
                $urlset->appendChild($url);
            }
        }


        /*$url = $dom->createElementNS($ns, 'url');
        $url->appendChild($dom->createElementNS($ns,'loc', 'http://new.com/ds'));
        $url->appendChild($dom->createElementNS($ns,'lastmod', date('Y-m-d', strtotime(time()))));
        $urlset->appendChild($url);*/

       /* foreach($cmspageCollection as $cmspage){
            $url = $dom->createElementNS(Zend_View_Helper_Navigation_Sitemap::SITEMAP_NS, 'url');
            $url->appendChild($dom->createElementNS(Zend_View_Helper_Navigation_Sitemap::SITEMAP_NS,'loc', $this->view->serverUrl() . $cmspage->getFeatureHref()));
            $url->appendChild($dom->createElementNS(Zend_View_Helper_Navigation_Sitemap::SITEMAP_NS,'lastmod', date('Y-m-d', strtotime($cmspage->modified_date))));
            $video = $dom->createElement('video:video');
            $video->appendChild($dom->createElement('video:thumbnail_loc', $this->view->serverUrl() . $cmspage->getImgUrl()));
            $video->appendChild($dom->createElement('video:title', $cmspage->getCategoriesString() . '/' . $cmspage->title));
            $video->appendChild($dom->createElement('video:description', $cmspage->description));
            $videoTable = Engine_Api::_()->getDbtable('files', 'storage');
            $videoSelect = $videoTable->select()
                ->where('parent_id = ?', $cmspage->video_id)
                ->where('parent_type = ?', 'video')
                ->where('mime_major = ?', 'unknown');
            $videoCollection = $videoTable->fetchAll($videoSelect);
            foreach($videoCollection as $__video){
                $video->appendChild($dom->createElement('video:content_loc', $__video->getHref()));
            }
            $cmspageVideo = $cmspage->getVideo();
            $video->appendChild($dom->createElement('video:duration', $cmspageVideo->duration));
            $video->appendChild($dom->createElement('video:live', 'no'));
            $url->appendChild($video);
            $urlset->appendChild($url);
        }*/
        $dom->appendChild($urlset);
        $xml = $dom->saveXML();

        $response = new \Zend\Http\Response();
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/xml; charset=utf-8');
        $response->setContent($xml);
        return $response;
    }
}
