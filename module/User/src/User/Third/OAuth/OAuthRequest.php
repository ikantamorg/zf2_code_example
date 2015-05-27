<?php
namespace User\Third\OAuth;

class OAuthRequest {

    protected $parameters;
    protected $http_method;
    protected $http_url;
    public $base_string;
    public static $version = '1.0';
    public static $POST_INPUT = 'php://input';

    function __construct($http_method, $http_url, $parameters=NULL) {
        $parameters = ($parameters) ? $parameters : array();
        $parameters = array_merge( \User\Third\OAuth\OAuthUtil::parse_parameters(parse_url($http_url, PHP_URL_QUERY)), $parameters);
        $this->parameters = $parameters;
        $this->http_method = $http_method;
        $this->http_url = $http_url;
    }

    public static function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) {
        $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
            ? 'http'
            : 'https';
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        }
        $http_url = ($http_url) ? $http_url : $scheme .
            '://' . $_SERVER['SERVER_NAME'] .
            ':' .
            $_SERVER['SERVER_PORT'] .
            $_SERVER['REQUEST_URI'];
        $http_method = ($http_method) ? $http_method : $_SERVER['REQUEST_METHOD'];
        if (!$parameters) {
            $request_headers = OAuthUtil::get_headers();
            $parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);
            if ($http_method == "POST"
                &&  isset($request_headers['Content-Type'])
                && strstr($request_headers['Content-Type'],
                    'application/x-www-form-urlencoded')
            ) {
                $post_data = OAuthUtil::parse_parameters(
                    file_get_contents(self::$POST_INPUT)
                );
                $parameters = array_merge($parameters, $post_data);
            }
            if (isset($request_headers['Authorization']) && substr($request_headers['Authorization'], 0, 6) == 'OAuth ') {
                $header_parameters = OAuthUtil::split_header(
                    $request_headers['Authorization']
                );
                $parameters = array_merge($parameters, $header_parameters);
            }
        }
        return new \User\Third\OAuth\OAuthRequest($http_method, $http_url, $parameters);
    }

    public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters=NULL) {
        $parameters = ($parameters) ?  $parameters : array();
        $defaults = array("oauth_version" => OAuthRequest::$version,
            "oauth_nonce" => OAuthRequest::generate_nonce(),
            "oauth_timestamp" => OAuthRequest::generate_timestamp(),
            "oauth_consumer_key" => $consumer->key);
        if ($token)
            $defaults['oauth_token'] = $token->key;

        $parameters = array_merge($defaults, $parameters);
        return new OAuthRequest($http_method, $http_url, $parameters);
    }

    public function set_parameter($name, $value, $allow_duplicates = true) {
        if ($allow_duplicates && isset($this->parameters[$name])) {
            if (is_scalar($this->parameters[$name])) {
                $this->parameters[$name] = array($this->parameters[$name]);
            }

            $this->parameters[$name][] = $value;
        } else {
            $this->parameters[$name] = $value;
        }
    }

    public function get_parameter($name) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    public function get_parameters() {
        return $this->parameters;
    }

    public function unset_parameter($name) {
        unset($this->parameters[$name]);
    }

    public function get_signable_parameters() {
        $params = $this->parameters;
        if (isset($params['oauth_signature'])) {
            unset($params['oauth_signature']);
        }
        return \User\Third\OAuth\OAuthUtil::build_http_query($params);
    }

    public function get_signature_base_string() {
        $parts = array(
            $this->get_normalized_http_method(),
            $this->get_normalized_http_url(),
            $this->get_signable_parameters()
        );
        $parts = \User\Third\OAuth\OAuthUtil::urlencode_rfc3986($parts);
        return implode('&', $parts);
    }

    public function get_normalized_http_method() {
        return strtoupper($this->http_method);
    }

    public function get_normalized_http_url() {
        $parts = parse_url($this->http_url);
        $scheme = (isset($parts['scheme'])) ? $parts['scheme'] : 'http';
        $port = (isset($parts['port'])) ? $parts['port'] : (($scheme == 'https') ? '443' : '80');
        $host = (isset($parts['host'])) ? strtolower($parts['host']) : '';
        $path = (isset($parts['path'])) ? $parts['path'] : '';

        if (($scheme == 'https' && $port != '443')
            || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }
        return "$scheme://$host$path";
    }

    public function to_url() {
        $post_data = $this->to_postdata();
        $out = $this->get_normalized_http_url();
        if ($post_data) {
            $out .= '?'.$post_data;
        }
        return $out;
    }

    public function to_postdata() {
        return OAuthUtil::build_http_query($this->parameters);
    }

    public function to_header($realm=null) {
        $first = true;
        if($realm) {
            $out = 'Authorization: OAuth realm="' . OAuthUtil::urlencode_rfc3986($realm) . '"';
            $first = false;
        } else
            $out = 'Authorization: OAuth';

        foreach ($this->parameters as $k => $v) {
            if (substr($k, 0, 5) != "oauth") continue;
            if (is_array($v)) {
                throw new \OAuthException('Arrays not supported in headers');
            }
            $out .= ($first) ? ' ' : ',';
            $out .= OAuthUtil::urlencode_rfc3986($k) .
                '="' .
                OAuthUtil::urlencode_rfc3986($v) .
                '"';
            $first = false;
        }
        return $out;
    }

    public function __toString() {
        return $this->to_url();
    }

    public function sign_request($signature_method, $consumer, $token) {
        $this->set_parameter(
            "oauth_signature_method",
            $signature_method->get_name(),
            false
        );
        $signature = $this->build_signature($signature_method, $consumer, $token);
        $this->set_parameter("oauth_signature", $signature, false);
    }

    public function build_signature($signature_method, $consumer, $token) {
        $signature = $signature_method->build_signature($this, $consumer, $token);
        return $signature;
    }

    private static function generate_timestamp() {
        return time();
    }

    private static function generate_nonce() {
        $mt = microtime();
        $rand = mt_rand();
        return md5($mt . $rand);
    }
}