<?php
namespace User\Third\OAuth;

class OAuthDataStore {
    function lookup_consumer($consumer_key) {
    }

    function lookup_token($consumer, $token_type, $token) {
    }

    function lookup_nonce($consumer, $token, $nonce, $timestamp) {
    }

    function new_request_token($consumer, $callback = null) {
    }

    function new_access_token($token, $consumer, $verifier = null) {
    }

}