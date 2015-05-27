<?php
namespace User\Hybrid;

class User
{
    public $providerId = NULL;
    public $timestamp = NULL;
    public $profile = NULL;

    function __construct()
    {
        $this->timestamp = time();
        $this->profile   = new UserProfile();
    }
}
