<?php
namespace User\Hybrid;

class UserActivity
{
    public $id = NULL;
    public $date = NULL;
    public $text = NULL;
    public $user = NULL;

    public function __construct()
    {
        $this->user = new stdClass();

        $this->user->identifier  = NULL;
        $this->user->displayName = NULL;
        $this->user->profileURL  = NULL;
        $this->user->photoURL    = NULL;
    }
}
