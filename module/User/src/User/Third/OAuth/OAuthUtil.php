<?php
namespace User\Third\OAuth;

class OAuthUtil {
    public static function urlencode_rfc3986($input) {
        if (is_array($input)) {
            return array_map(array('OAuthUtil', 'urlencode_rfc3986'), $input);
        } else if (is_scalar($input)) {
            return str_replace(
                '+',
                ' ',
                str_replace('%7E', '~', rawurlencode($input))
            );
        } else {
            return '';
        }
    }
    public static function urldecode_rfc3986($string) {
        return urldecode($string);
    }
    public static function split_header($header, $only_allow_oauth_parameters = true) {
        $params = array();
        if (preg_match_all('/('.($only_allow_oauth_parameters ? 'oauth_' : '').'[a-z_-]*)=(:?"([^"]*)"|([^,]*))/', $header, $matches)) {
            foreach ($matches[1] as $i => $h) {
                $params[$h] = \User\Third\OAuth\OAuthUtil::urldecode_rfc3986(empty($matches[3][$i]) ? $matches[4][$i] : $matches[3][$i]);
            }
            if (isset($params['realm'])) {
                unset($params['realm']);
            }
        }
        return $params;
    }

    public static function get_headers() {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $out = array();
            foreach ($headers AS $key => $value) {
                $key = str_replace(
                    " ",
                    "-",
                    ucwords(strtolower(str_replace("-", " ", $key)))
                );
                $out[$key] = $value;
            }
        } else {
            $out = array();
            if( isset($_SERVER['CONTENT_TYPE']) )
                $out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
            if( isset($_ENV['CONTENT_TYPE']) )
                $out['Content-Type'] = $_ENV['CONTENT_TYPE'];

            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == "HTTP_") {
                    $key = str_replace(
                        " ",
                        "-",
                        ucwords(strtolower(str_replace("_", " ", substr($key, 5))))
                    );
                    $out[$key] = $value;
                }
            }
        }
        return $out;
    }

    public static function parse_parameters( $input ) {
        if (!isset($input) || !$input) return array();

        $pairs = explode('&', $input);

        $parsed_parameters = array();
        foreach ($pairs as $pair) {
            $split = explode('=', $pair, 2);
            $parameter = \User\Third\OAuth\OAuthUtil::urldecode_rfc3986($split[0]);
            $value = isset($split[1]) ? \User\Third\OAuth\OAuthUtil::urldecode_rfc3986($split[1]) : '';

            if (isset($parsed_parameters[$parameter])) {
                if (is_scalar($parsed_parameters[$parameter])) {
                    $parsed_parameters[$parameter] = array($parsed_parameters[$parameter]);
                }
                $parsed_parameters[$parameter][] = $value;
            } else {
                $parsed_parameters[$parameter] = $value;
            }
        }
        return $parsed_parameters;
    }

    public static function build_http_query($params) {
        if (!$params) return '';
        $keys = \User\Third\OAuth\OAuthUtil::urlencode_rfc3986(array_keys($params));
        $values = \User\Third\OAuth\OAuthUtil::urlencode_rfc3986(array_values($params));
        $params = array_combine($keys, $values);
        uksort($params, 'strcmp');

        $pairs = array();
        foreach ($params as $parameter => $value) {
            if (is_array($value)) {
                sort($value, SORT_STRING);
                foreach ($value as $duplicate_value) {
                    $pairs[] = $parameter . '=' . $duplicate_value;
                }
            } else {
                $pairs[] = $parameter . '=' . $value;
            }
        }
        return implode('&', $pairs);
    }
}