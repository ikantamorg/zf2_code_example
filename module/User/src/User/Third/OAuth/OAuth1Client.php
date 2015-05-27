<?php
namespace User\Third\OAuth;

class OAuth1Client{
    public $api_base_url          = "";
    public $authorize_url         = "";
    public $authenticate_url      = "";
    public $request_token_url     = "";
    public $access_token_url      = "";
    public $request_token_method  = "GET";
    public $access_token_method   = "GET";
    public $redirect_uri          = "";
    public $decode_json           = true;
    public $curl_time_out         = 30;
    public $curl_connect_time_out = 30;
    public $curl_ssl_verifypeer   = false;
    public $curl_auth_header      = true;
    public $curl_useragent        = "OAuth/1 Simple PHP Client v0.1; HybridAuth http://hybridauth.sourceforge.net/";
    public $curl_proxy            = null;
    public $http_code             = "";
    public $http_info             = "";
    protected $response           = null;

    function __construct( $consumer_key, $consumer_secret, $oauth_token = null, $oauth_token_secret = null )
    {
        $this->sha1_method = new \User\Third\OAuth\OAuthSignatureMethodHMACSHA1();
        $this->consumer    = new \User\Third\OAuth\OAuthConsumer( $consumer_key, $consumer_secret );
        $this->token       = null;
        if ( $oauth_token && $oauth_token_secret ){
            $this->token = new \User\Third\OAuth\OAuthConsumer( $oauth_token, $oauth_token_secret );
        }
    }

    function authorizeUrl( $token, $extras =array() )
    {
        if ( is_array( $token ) ){
            $token = $token['oauth_token'];
        }
        $parameters = array( "oauth_token" => $token );
        if( count($extras) )
            foreach( $extras as $k=>$v )
                $parameters[$k] = $v;

        return $this->authorize_url . "?" . http_build_query( $parameters );
    }

    function requestToken( $callback = null )
    {
        $parameters = array();
        if ( $callback ) {
            $this->redirect_uri = $parameters['oauth_callback'] = $callback;
        }

        $request     = $this->signedRequest( $this->request_token_url, $this->request_token_method, $parameters );
        $token       = \User\Third\OAuth\OAuthUtil::parse_parameters( $request );
        $this->token = new \User\Third\OAuth\OAuthConsumer( $token['oauth_token'], $token['oauth_token_secret'] );

        return $token;
    }

    function accessToken( $oauth_verifier = false, $oauth_token = false )
    {
        $parameters = array();
        if ( $oauth_verifier ) {
            $parameters['oauth_verifier'] = $oauth_verifier;
        }
        $request     = $this->signedRequest( $this->access_token_url, $this->access_token_method, $parameters );
        $token       = \User\Third\OAuth\OAuthUtil::parse_parameters( $request );
        $this->token = new \User\Third\OAuth\OAuthConsumer( $token['oauth_token'], $token['oauth_token_secret'] );
        return $token;
    }

    function get($url, $parameters = array(), $content_type = NULL)
    {
        return $this->api($url, 'GET', $parameters, NULL, $content_type);
    }

    function post($url, $parameters = array(), $body = NULL, $content_type = NULL, $multipart = false)
    {
        return $this->api($url, 'POST', $parameters, $body, $content_type, $multipart );
    }

    function api( $url, $method = 'GET', $parameters = array(), $body = NULL, $content_type = NULL, $multipart = false )
    {
        if ( strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0 ) {
            $url = $this->api_base_url . $url;
        }

        $response = $this->signedRequest( $url, $method, $parameters, $body, $content_type, $multipart );

        if( $this->decode_json ){
            $response = json_decode( $response );
        }
        return $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    function signedRequest( $url, $method, $parameters, $body = NULL, $content_type = NULL, $multipart = false )
    {
        $signature_parameters = array();
        foreach( $parameters AS $key => $value ){
            if( !$multipart || strpos( $key, 'oauth_' ) === 0 ){
                $signature_parameters[$key] = $value;
            }
        }

        $request = \User\Third\OAuth\OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $signature_parameters);
        $request->sign_request($this->sha1_method, $this->consumer, $this->token);
        switch ($method) {
            case 'GET': return $this->request( $request->to_url(), 'GET', NULL, NULL, $content_type );
            default   :
                if ($body)
                    return $this->request( $request->to_url(), $method, $body, $request->to_header(), $content_type );
                else
                    return $this->request( $request->get_normalized_http_url(), $method, ($multipart ? $parameters : $request->to_postdata()), $request->to_header(), $content_type, $multipart ) ;
        }
    }

    function request( $url, $method, $postfields = NULL, $auth_header = NULL, $content_type = NULL, $multipart = false )
    {
        $this->http_info = array();
        $ci = curl_init();

        curl_setopt( $ci, CURLOPT_USERAGENT     , $this->curl_useragent );
        curl_setopt( $ci, CURLOPT_CONNECTTIMEOUT, $this->curl_connect_time_out );
        curl_setopt( $ci, CURLOPT_TIMEOUT       , $this->curl_time_out );
        curl_setopt( $ci, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ci, CURLOPT_HTTPHEADER    , array('Expect:') );
        curl_setopt( $ci, CURLOPT_SSL_VERIFYPEER, $this->curl_ssl_verifypeer );
        curl_setopt( $ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader') );
        curl_setopt( $ci, CURLOPT_HEADER        , FALSE );

        if( $multipart ){
            curl_setopt( $ci, CURLOPT_HTTPHEADER, array( 'Expect:', $auth_header ) );

        }elseif ($content_type)
            curl_setopt( $ci, CURLOPT_HTTPHEADER, array('Expect:', "Content-Type: $content_type") );

        if($this->curl_proxy){
            curl_setopt( $ci, CURLOPT_PROXY        , $this->curl_proxy);
        }

        switch ($method){
            case 'POST':
                curl_setopt( $ci, CURLOPT_POST, TRUE );

                if ( !empty($postfields) ){
                    curl_setopt( $ci, CURLOPT_POSTFIELDS, $postfields );
                }

                if ( !empty($auth_header) && $this->curl_auth_header && !$multipart ){
                    curl_setopt( $ci, CURLOPT_HTTPHEADER, array( 'Content-Type: application/atom+xml', $auth_header ) );
                }
                break;
            case 'DELETE':
                curl_setopt( $ci, CURLOPT_CUSTOMREQUEST, 'DELETE' );
                if ( !empty($postfields) ){
                    $url = "{$url}?{$postfields}";
                }
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);

        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));

        curl_close ($ci);

        return $response;
    }

    function getHeader($ch, $header) {
        $i = strpos($header, ':');

        if ( !empty($i) ){
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
}
