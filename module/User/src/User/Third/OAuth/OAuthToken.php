<?php
namespace User\Third\OAuth;

class OAuthToken {

    public $key;
    public $secret;

    function __construct($key, $secret) {
        $this->key = $key;
        $this->secret = $secret;
    }

    function to_string() {
        return "oauth_token=" .
        \User\Third\OAuth\OAuthUtil::urlencode_rfc3986($this->key) .
        "&oauth_token_secret=" .
        \User\Third\OAuth\OAuthUtil::urlencode_rfc3986($this->secret);
    }

    function __toString() {
        return $this->to_string();
    }
}