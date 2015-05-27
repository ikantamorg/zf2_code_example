<?php
namespace User\Provider\Model;

class OAuth2 extends \User\Provider\Model\Model
{
    public $scope = "";

    function errorMessageByStatus( $code = null ) {
        $http_status_codes = ARRAY(
            200 => "OK: Success!",
            304 => "Not Modified: There was no new data to return.",
            400 => "Bad Request: The request was invalid.",
            401 => "Unauthorized.",
            403 => "Forbidden: The request is understood, but it has been refused.",
            404 => "Not Found: The URI requested is invalid or the resource requested does not exists.",
            406 => "Not Acceptable.",
            500 => "Internal Server Error: Something is broken.",
            502 => "Bad Gateway.",
            503 => "Service Unavailable."
        );

        if( ! $code && $this->api )
            $code = $this->api->http_code;

        if( isset( $http_status_codes[ $code ] ) )
            return $code . " " . $http_status_codes[ $code ];
    }

    function initialize()
    {
        if ( ! $this->config["keys"]["id"] || ! $this->config["keys"]["secret"] ){
            throw new \Exception( "Your application id and secret are required in order to connect to {$this->providerId}.", 4 );
        }

        if( isset( $this->config["scope"] ) && ! empty( $this->config["scope"] ) ){
            $this->scope = $this->config["scope"];
        }

        $this->api = new \User\Third\OAuth\OAuth2Client( $this->config["keys"]["id"], $this->config["keys"]["secret"], $this->endpoint );

        if( $this->token( "access_token" ) ){
            $this->api->access_token            = $this->token( "access_token" );
            $this->api->refresh_token           = $this->token( "refresh_token" );
            $this->api->access_token_expires_in = $this->token( "expires_in" );
            $this->api->access_token_expires_at = $this->token( "expires_at" );
        }

        if( isset( \User\Hybrid\Auth::$config["proxy"] ) ){
            $this->api->curl_proxy = \User\Hybrid\Auth::$config["proxy"];
        }
    }

    function loginBegin()
    {
        \User\Hybrid\Auth::redirect( $this->api->authorizeUrl( array( "scope" => $this->scope ) ) );
    }

    function loginFinish()
    {
        $error = (array_key_exists('error',$_REQUEST))?$_REQUEST['error']:"";

        if ( $error ){
            throw new \Exception( "Authentication failed! {$this->providerId} returned an error: $error", 5 );
        }

        $code = (array_key_exists('code',$_REQUEST))?$_REQUEST['code']:"";

        try{
            $this->api->authenticate( $code );
        } catch( \Exception $e ) {
            throw new \Exception( "User profile request failed! {$this->providerId} returned an error: $e", 6 );
        }

        if ( ! $this->api->access_token ){
            throw new \Exception( "Authentication failed! {$this->providerId} returned an invalid access token.", 5 );
        }

        $this->token( "access_token" , $this->api->access_token  );
        $this->token( "refresh_token", $this->api->refresh_token );
        $this->token( "expires_in"   , $this->api->access_token_expires_in );
        $this->token( "expires_at"   , $this->api->access_token_expires_at );

        $this->setUserConnected();
    }

    function refreshToken()
    {
        if( $this->api->access_token ){
            if( $this->api->refresh_token && $this->api->access_token_expires_at ){
                if( $this->api->access_token_expires_at <= time() ){
                    $response = $this->api->refreshToken( array( "refresh_token" => $this->api->refresh_token ) );
                    if( ! isset( $response->access_token ) || ! $response->access_token ){
                        $this->setUserUnconnected();
                        throw new \Exception( "The Authorization Service has return an invalid response while requesting a new access token. " . (string) $response->error );
                    }

                    $this->api->access_token = $response->access_token;

                    if( isset( $response->refresh_token ) )
                        $this->api->refresh_token = $response->refresh_token;

                    if( isset( $response->expires_in ) ){
                        $this->api->access_token_expires_in = $response->expires_in;
                        $this->api->access_token_expires_at = time() + $response->expires_in;
                    }
                }
            }

            $this->token( "access_token" , $this->api->access_token  );
            $this->token( "refresh_token", $this->api->refresh_token );
            $this->token( "expires_in"   , $this->api->access_token_expires_in );
            $this->token( "expires_at"   , $this->api->access_token_expires_at );
        }
    }
}
