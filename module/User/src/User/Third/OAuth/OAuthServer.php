<?php
namespace User\Third\OAuth;

class OAuthServer {
    protected $timestamp_threshold = 300; // in seconds, five minutes
    protected $version = '1.0';             // hi blaine
    protected $signature_methods = array();

    protected $data_store;

    function __construct($data_store) {
        $this->data_store = $data_store;
    }

    public function add_signature_method($signature_method) {
        $this->signature_methods[$signature_method->get_name()] =
            $signature_method;
    }

    public function fetch_request_token(&$request) {
        $this->get_version($request);
        $consumer = $this->get_consumer($request);
        $token = NULL;
        $this->check_signature($request, $consumer, $token);
        $callback = $request->get_parameter('oauth_callback');
        $new_token = $this->data_store->new_request_token($consumer, $callback);
        return $new_token;
    }

    public function fetch_access_token(&$request) {
        $this->get_version($request);
        $consumer = $this->get_consumer($request);
        $token = $this->get_token($request, $consumer, "request");
        $this->check_signature($request, $consumer, $token);
        $verifier = $request->get_parameter('oauth_verifier');
        $new_token = $this->data_store->new_access_token($token, $consumer, $verifier);
        return $new_token;
    }

    public function verify_request(&$request) {
        $this->get_version($request);
        $consumer = $this->get_consumer($request);
        $token = $this->get_token($request, $consumer, "access");
        $this->check_signature($request, $consumer, $token);
        return array($consumer, $token);
    }

    private function get_version(&$request) {
        $version = $request->get_parameter("oauth_version");
        if (!$version) {
            $version = '1.0';
        }
        if ($version !== $this->version) {
            throw new \User\Third\OAuth\OAuthException("OAuth version '$version' not supported");
        }
        return $version;
    }

    private function get_signature_method($request) {
        $signature_method = $request instanceof \User\Third\OAuth\OAuthRequest
            ? $request->get_parameter("oauth_signature_method")
            : NULL;

        if (!$signature_method) {
            throw new \User\Third\OAuth\OAuthException('No signature method parameter. This parameter is required');
        }

        if (!in_array($signature_method,
            array_keys($this->signature_methods))) {
            throw new \User\Third\OAuth\OAuthException(
                "Signature method '$signature_method' not supported " .
                "try one of the following: " .
                implode(", ", array_keys($this->signature_methods))
            );
        }
        return $this->signature_methods[$signature_method];
    }

    /**
     * try to find the consumer for the provided request's consumer key
     */
    private function get_consumer($request) {
        $consumer_key = $request instanceof \User\Third\OAuth\OAuthRequest
            ? $request->get_parameter("oauth_consumer_key")
            : NULL;

        if (!$consumer_key) {
            throw new \User\Third\OAuth\OAuthException("Invalid consumer key");
        }

        $consumer = $this->data_store->lookup_consumer($consumer_key);
        if (!$consumer) {
            throw new \User\Third\OAuth\OAuthException("Invalid consumer");
        }

        return $consumer;
    }

    private function get_token($request, $consumer, $token_type="access") {
        $token_field = $request instanceof \User\Third\OAuth\OAuthRequest
            ? $request->get_parameter('oauth_token')
            : NULL;

        $token = $this->data_store->lookup_token(
            $consumer, $token_type, $token_field
        );
        if (!$token) {
            throw new \User\Third\OAuth\OAuthException("Invalid $token_type token: $token_field");
        }
        return $token;
    }

    private function check_signature($request, $consumer, $token) {
        $timestamp = $request instanceof \User\Third\OAuth\OAuthRequest
            ? $request->get_parameter('oauth_timestamp')
            : NULL;
        $nonce = $request instanceof \User\Third\OAuth\OAuthRequest
            ? $request->get_parameter('oauth_nonce')
            : NULL;

        $this->check_timestamp($timestamp);
        $this->check_nonce($consumer, $token, $nonce, $timestamp);

        $signature_method = $this->get_signature_method($request);

        $signature = $request->get_parameter('oauth_signature');
        $valid_sig = $signature_method->check_signature(
            $request,
            $consumer,
            $token,
            $signature
        );

        if (!$valid_sig) {
            throw new \User\Third\OAuth\OAuthException("Invalid signature");
        }
    }

    private function check_timestamp($timestamp) {
        if( ! $timestamp )
            throw new \User\Third\OAuth\OAuthException(
                'Missing timestamp parameter. The parameter is required'
            );

        $now = time();
        if (abs($now - $timestamp) > $this->timestamp_threshold) {
            throw new \User\Third\OAuth\OAuthException(
                "Expired timestamp, yours $timestamp, ours $now"
            );
        }
    }

    private function check_nonce($consumer, $token, $nonce, $timestamp) {
        if( ! $nonce )
            throw new \User\Third\OAuth\OAuthException(
                'Missing nonce parameter. The parameter is required'
            );

        $found = $this->data_store->lookup_nonce(
            $consumer,
            $token,
            $nonce,
            $timestamp
        );
        if ($found) {
            throw new \User\Third\OAuth\OAuthException("Nonce already used: $nonce");
        }
    }

}