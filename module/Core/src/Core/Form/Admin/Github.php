<?php

namespace Core\Form\Admin;

use Core\Form\AbstractForm as Form;
use Zend\Form\Element;

class Github extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->add(new Element\Text('github_remote', ['label' => 'GitHub remote']));
        $this->add(new Element\Text('github_branch', ['label' => 'GitHub branch']));
        $this->add(new Element\Text('github_executable_path', ['label' => 'GitHub executable path']));
        $this->add(new Element\Text('github_username', ['label' => 'GitHub username']));
        $this->add(new Element\Text('github_password', ['label' => 'GitHub password']));
    }

}