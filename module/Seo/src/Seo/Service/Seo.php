<?php
namespace Seo\Service;

use Core\Service\AbstractService;
use Core\Traits\ServiceOption;
use Core\Traits\ServiceDoctrine;

class Seo extends AbstractService
{
    use ServiceOption;
    use ServiceDoctrine;


    protected static $sitemap = [];
    protected static $description = '';
    protected static $keywords = [];
    protected static $meta = [];
    protected static $title = [];
    protected static $image;


    public function addSitemapElement($element)
    {
        self::$sitemap[] = $element;
    }

    public function getSitemapElements()
    {
        return self::$sitemap;
    }

    public function setDescription($description)
    {
        self::$description = $description;
    }

    public function addMeta($name, $content)
    {
        self::$meta[$name] = $content;
    }

    public function setKeywords($keywords = [])
    {
        self::$keywords = $keywords;
    }

    public function addKeyword($keyword)
    {
        self::$keywords[] = $keyword;
    }

    public function addTitle($title)
    {
        self::$title[] = $title;
    }

    public function getDescription()
    {
        return self::$description;
    }

    public function getTitle()
    {
        return self::$title;
    }

    public function getKeywords()
    {
        return self::$keywords;
    }

    public function setImage($image)
    {
        self::$image = $image;
    }

    public function getImage()
    {
        return self::$image;
    }

    public function getImageHref()
    {
        if($this->getImage()){
            return $this->getImage();
        } else {
            $image = $this->getServiceDoctrine()->getEntity('Storage', 'File', $this->getServiceOption()->get('seo', 'share_image_file_id')->getValue());
            return $image ? $image->getHref() : '';
        }
    }

    public function getKeywordsString()
    {
        $arrayKeywords = $this->getKeywords();
        $arrayKeywords = empty($arrayKeywords) ? explode(',', $this->getServiceOption()->get('seo', 'keywords')->getValue()) : $arrayKeywords;
        return implode(', ', $arrayKeywords);
    }

    public function getTitleString()
    {
        $arrayTitle = [$this->getServiceOption()->get('seo', 'title')->getValue()];
        $arrayTitle = array_merge($arrayTitle, $this->getTitle());
        return implode($this->getServiceOption()->get('seo', 'title_separator')->getValue(), $arrayTitle);
    }

    public function getDescriptionString()
    {
        $description =  $this->getDescription();
        return $description ? $description : $this->getServiceOption()->get('seo', 'description')->getValue();
    }
}