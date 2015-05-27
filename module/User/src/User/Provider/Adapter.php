<?php
namespace User\Provider;

class Adapter
{
    public $id       = NULL ;
    public $config   = NULL ;
    public $params   = array() ;
    public $wrapper  = NULL ;
    public $adapter  = NULL ;

    function factory( $id, $params = array() )
    {
        $this->id     = $id;
        $this->params = $params;
        $this->id     = $this->getProviderCiId( $this->id );
        $this->config = $this->getConfigById( $this->id );

        if( ! $this->id ){
            throw new \User\Hybrid\Exception( "No provider ID specified.", 2 );
        }

        if( ! $this->config ){
            throw new \User\Hybrid\Exception( "Unknown Provider ID, check your configuration file.", 3 );
        }

        if( ! $this->config["enabled"] ){
            throw new \User\Hybrid\Exception( "The provider '{$this->id}' is not enabled.", 3 );
        }

        $this->wrapper = "\\User\\Provider\\" . $this->id;
        $this->adapter = new $this->wrapper( $this->id, $this->config, $this->params );

        return $this;
    }

    function login()
    {
        if( ! $this->adapter ){
            throw new \User\Hybrid\Exception( "Hybrid_Provider_Adapter::login() should not directly used." );
        }

        foreach( \User\Hybrid\Auth::$config["providers"] as $idpid => $params ){
            \User\Hybrid\Auth::storage()->delete( "hauth_session.{$idpid}.hauth_return_to"    );
            \User\Hybrid\Auth::storage()->delete( "hauth_session.{$idpid}.hauth_endpoint"     );
            \User\Hybrid\Auth::storage()->delete( "hauth_session.{$idpid}.id_provider_params" );
        }

        $this->logout();

        if (empty(\User\Hybrid\Auth::$config["base_url"])) {
            $url  = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http' : 'https';
            $url .= '://' . $_SERVER['HTTP_HOST'];
            $url .= $_SERVER['REQUEST_URI'];
            $HYBRID_AUTH_URL_BASE = $url;
        } else {
            $HYBRID_AUTH_URL_BASE = \User\Hybrid\Auth::$config["base_url"];
        }

        if( !is_array( $this->params ) ){
            $this->params = array();
        }

        $this->params["hauth_token"] = session_id();
        $this->params["hauth_time"]  = time();
        $this->params["login_start"] = $HYBRID_AUTH_URL_BASE . ( strpos( $HYBRID_AUTH_URL_BASE, '?' ) ? '&' : '?' ) . "hauth.start={$this->id}&hauth.time={$this->params["hauth_time"]}";
        $this->params["login_done"]  = $HYBRID_AUTH_URL_BASE . ( strpos( $HYBRID_AUTH_URL_BASE, '?' ) ? '&' : '?' ) . "hauth.done={$this->id}";

        if( isset( $this->params["hauth_return_to"] ) ){
            \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->id}.hauth_return_to", $this->params["hauth_return_to"] );
        }
        if( isset( $this->params["login_done"] ) ){
            \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->id}.hauth_endpoint" , $this->params["login_done"] );
        }

        \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->id}.id_provider_params" , $this->params );
        \User\Hybrid\Auth::storage()->config( "CONFIG", \User\Hybrid\Auth::$config );
        \User\Hybrid\Auth::redirect( $this->params["login_start"] );
    }

    function logout()
    {
        $this->adapter->logout();
    }

    public function isUserConnected()
    {
        return $this->adapter->isUserConnected();
    }

    public function __call( $name, $arguments )
    {
        if ( ! $this->isUserConnected() ){
            throw new \User\Hybrid\Exception( "User not connected to the provider {$this->id}.", 7 );
        }

        if ( ! method_exists( $this->adapter, $name ) ){
            throw new \User\Hybrid\Exception( "Call to undefined function Hybrid_Providers_{$this->id}::$name()." );
        }

        $counter = count( $arguments );
        if( $counter == 1 ){
            return $this->adapter->$name( $arguments[0] );
        } elseif( $counter == 2 ) {
            return $this->adapter->$name( $arguments[0], $arguments[1] );
        } else {
            return $this->adapter->$name();
        }
    }

    public function getAccessToken()
    {
        if( ! $this->adapter->isUserConnected() ){
            throw new \User\Hybrid\Exception( "User not connected to the provider.", 7 );
        }

        return
            ARRAY(
                "access_token"        => $this->adapter->token( "access_token" )       , // OAuth access token
                "access_token_secret" => $this->adapter->token( "access_token_secret" ), // OAuth access token secret
                "refresh_token"       => $this->adapter->token( "refresh_token" )      , // OAuth refresh token
                "expires_in"          => $this->adapter->token( "expires_in" )         , // OPTIONAL. The duration in seconds of the access token lifetime
                "expires_at"          => $this->adapter->token( "expires_at" )         , // OPTIONAL. Timestamp when the access_token expire. if not provided by the social api, then it should be calculated: expires_at = now + expires_in
            );
    }

    function api()
    {
        if( ! $this->adapter->isUserConnected() ){
            \User\Hybrid\Logger::error( "User not connected to the provider." );

            throw new \User\Hybrid\Exception( "User not connected to the provider.", 7 );
        }
        return $this->adapter->api;
    }

    function returnToCallbackUrl()
    {
        $callback_url = \User\Hybrid\Auth::storage()->get( "hauth_session.{$this->id}.hauth_return_to" );

        \User\Hybrid\Auth::storage()->delete( "hauth_session.{$this->id}.hauth_return_to"    );
        \User\Hybrid\Auth::storage()->delete( "hauth_session.{$this->id}.hauth_endpoint"     );
        \User\Hybrid\Auth::storage()->delete( "hauth_session.{$this->id}.id_provider_params" );
        \User\Hybrid\Auth::redirect( $callback_url );
    }

    function getConfigById( $id )
    {
        if( isset( \User\Hybrid\Auth::$config["providers"][$id] ) ){
            return \User\Hybrid\Auth::$config["providers"][$id];
        }
        return NULL;
    }

    function getProviderCiId( $id )
    {
        foreach( \User\Hybrid\Auth::$config["providers"] as $idpid => $params ){
            if( strtolower( $idpid ) == strtolower( $id ) ){
                return $idpid;
            }
        }
        return NULL;
    }
}
