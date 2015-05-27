<?php
namespace User\Hybrid;

class Auth
{
    public static $version = "2.4.1";
    public static $config  = array();
    public static $store   = NULL;
    public static $error   = NULL;
    public static $logger  = NULL;

    function __construct( $config )
    {
        Auth::initialize( $config );
    }

    public static function initialize( $config )
    {
        if( ! is_array( $config ) && ! file_exists( $config ) ){
            throw new Exception( "Hybriauth config does not exist on the given path.", 1 );
        }

        if( ! is_array( $config ) ){
            $config = include $config;
        }

        $config["path_base"]        = realpath( dirname( __FILE__ ) )  . "/";
        $config["path_libraries"]   = $config["path_base"] . "thirdparty/";
        $config["path_resources"]   = $config["path_base"] . "resources/";
        $config["path_providers"]   = $config["path_base"] . "Providers/";

        if( ! isset( $config["debug_mode"] ) ){
            $config["debug_mode"] = false;
            $config["debug_file"] = null;
        }

        Auth::$config = $config;
        Auth::$logger = new Logger();
        Auth::$error = new Error();
        Auth::$store = new Storage();

        if( Error::hasError() ){
            $m = Error::getErrorMessage();
            $c = Error::getErrorCode();
            $p = Error::getErrorPrevious();

            Error::clearError();

            if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) && ($p instanceof Exception) ) {
                throw new Exception( $m, $c, $p );
            }
            else{
                throw new Exception( $m, $c );
            }
        }
    }

    public static function storage()
    {
        return Auth::$store;
    }

    function getSessionData()
    {
        return Auth::storage()->getSessionData();
    }

    function restoreSessionData( $sessiondata = NULL )
    {
        Auth::storage()->restoreSessionData( $sessiondata );
    }

    public static function authenticate( $providerId, $params = NULL )
    {
        if( ! Auth::storage()->get( "hauth_session.$providerId.is_logged_in" ) ){
            $provider_adapter = Auth::setup( $providerId, $params );
            $provider_adapter->login();
        } else {
            return Auth::getAdapter( $providerId );
        }
    }

    public static function getAdapter( $providerId = NULL )
    {
        return Auth::setup( $providerId );
    }

    public static function setup( $providerId, $params = NULL )
    {
        if( ! $params ){
            $params = Auth::storage()->get( "hauth_session.$providerId.id_provider_params" );
        }

        if( ! $params ){
            $params = ARRAY();
        }

        if( is_array($params) && ! isset( $params["hauth_return_to"] ) ){
            $params["hauth_return_to"] = Auth::getCurrentUrl();
        }

        $provider   = new \User\Provider\Adapter();
        $provider->factory( $providerId, $params );
        return $provider;
    }

    public static function isConnectedWith( $providerId )
    {
        return (bool) Auth::storage()->get( "hauth_session.{$providerId}.is_logged_in" );
    }

    public static function getConnectedProviders()
    {
        $idps = array();
        foreach( Auth::$config["providers"] as $idpid => $params ){
            if( Auth::isConnectedWith( $idpid ) ){
                $idps[] = $idpid;
            }
        }
        return $idps;
    }

    public static function getProviders()
    {
        $idps = array();
        foreach( Auth::$config["providers"] as $idpid => $params ){
            if($params['enabled']) {
                $idps[$idpid] = array( 'connected' => false );
                if( Auth::isConnectedWith( $idpid ) ){
                    $idps[$idpid]['connected'] = true;
                }
            }
        }
        return $idps;
    }

    public static function logoutAllProviders()
    {
        $idps = Auth::getConnectedProviders();
        foreach( $idps as $idp ){
            $adapter = Auth::getAdapter( $idp );

            $adapter->logout();
        }
    }

    public static function redirect( $url, $mode = "PHP" )
    {
        if( $mode == "PHP" ){
            header( "Location: $url" ) ;
        } elseif( $mode == "JS" ) {
            echo '<html>';
            echo '<head>';
            echo '<script type="text/javascript">';
            echo 'function redirect(){ window.top.location.href="' . $url . '"; }';
            echo '</script>';
            echo '</head>';
            echo '<body onload="redirect()">';
            echo 'Redirecting, please wait...';
            echo '</body>';
            echo '</html>';
        }
        die();
    }

    public static function getCurrentUrl( $request_uri = true )
    {
        if (php_sapi_name() == 'cli') {
            return '';
        }

        $protocol = 'http://';

        if(
            (isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 )) ||
            (isset( $_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ){
            $protocol = 'https://';
        }

        $url = $protocol . $_SERVER['HTTP_HOST'];

        if( $request_uri ){
            $url .= $_SERVER['REQUEST_URI'];
        } else{
            $url .= $_SERVER['PHP_SELF'];
        }

        return $url;
    }
}
