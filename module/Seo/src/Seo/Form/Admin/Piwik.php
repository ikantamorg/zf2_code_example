<?php

namespace Seo\Form\Admin;

use Admin\Form\AbstractForm as Form;
use Zend\Form\Element;

class Piwik extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->add(new Element\Text('piwik_server_url', ['label' => 'Server URL']));
        $this->add(new Element\Text('piwik_access_token', ['label' => 'Access token']));
        $this->add(new Element\Text('piwik_site_id', ['label' => 'Site Id']));
    }
}