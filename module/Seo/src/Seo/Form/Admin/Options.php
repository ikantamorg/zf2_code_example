<?php

namespace Seo\Form\Admin;

use Admin\Form\AbstractForm as Form;
use Zend\Form\Element;

class Options extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->add(new Element\Text('title', ['label' => 'Site title']));
        $this->add(new Element\Text('title_separator', ['label' => 'Title separator']));
        $this->add(new Element\Textarea('description', ['label' => 'Site description']));
        $this->add(new Element\Textarea('share_image_file_id', ['label' => 'Share image']));

        $keywords = new Element\Text('keywords', ['label' => 'Site keywords']);
        $keywords->setAttribute('data-role', 'tagsinput');
        $this->add($keywords);
    }
}