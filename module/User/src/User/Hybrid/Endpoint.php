<?php
namespace User\Hybrid;

class Endpoint {

    protected $request = NULL;
    protected $initDone = FALSE;

    public function __construct( $request = NULL )
    {
        if ( is_null($request) ){
            $request = $_REQUEST;
            if ( strrpos( $_SERVER["QUERY_STRING"], '?' ) ) {
                $_SERVER["QUERY_STRING"] = str_replace( "?", "&", $_SERVER["QUERY_STRING"] );
                parse_str( $_SERVER["QUERY_STRING"], $request );
            }
        }

        $this->request = $request;

        if ( isset( $this->request["get"] ) && $this->request["get"] == "openid_policy" ) {
            $this->processOpenidPolicy();
        }

        if ( isset( $this->request["get"] ) && $this->request["get"] == "openid_xrds" ) {
            $this->processOpenidXRDS();
        }

        if ( isset( $this->request["hauth_start"] ) && $this->request["hauth_start"] ) {
            $this->processAuthStart();
        } elseif ( isset( $this->request["hauth_done"] ) && $this->request["hauth_done"] ) {
            $this->processAuthDone();
        } else {
            $this->processOpenidRealm();
        }
    }

    public static function process( $request = NULL )
    {
        $class = function_exists('get_called_class') ? get_called_class() : __CLASS__;
        new $class( $request );
    }

    protected function processOpenidPolicy()
    {
        $output = file_get_contents( dirname(__FILE__) . "/resources/openid_policy.html" );
        print $output;
        die();
    }

    protected function processOpenidXRDS()
    {
        header("Content-Type: application/xrds+xml");
        $output = str_replace(
            "{RETURN_TO_URL}",
            str_replace(
                array("<", ">", "\"", "'", "&"), array("&lt;", "&gt;", "&quot;", "&apos;", "&amp;"),
                Auth::getCurrentUrl( false )
            ),
            file_get_contents( dirname(__FILE__) . "/resources/openid_xrds.xml" )
        );
        print $output;
        die();
    }

    protected function processOpenidRealm()
    {
        $output = str_replace(
            "{X_XRDS_LOCATION}",
            htmlentities( Auth::getCurrentUrl( false ), ENT_QUOTES, 'UTF-8' ) . "?get=openid_xrds&v=" . Auth::$version,
            file_get_contents( dirname(__FILE__) . "/resources/openid_realm.html" )
        );
        print $output;
        die();
    }

    protected function processAuthStart()
    {
        $this->authInit();
        $provider_id = trim( strip_tags( $this->request["hauth_start"] ) );
        if( ! Auth::storage()->get( "hauth_session.$provider_id.hauth_endpoint" ) ) {
            throw new Exception( "You cannot access this page directly." );
        }

        $hauth = Auth::setup( $provider_id );
        if( ! $hauth ) {
            throw new Exception( "Invalid parameter! Please return to the login page and try again." );
        }

        try {
            $hauth->adapter->loginBegin();
        } catch ( Exception $e ) {
            Error::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString(), $e->getPrevious() );

            $hauth->returnToCallbackUrl();
        }
        die();
    }

    protected function processAuthDone()
    {
        $this->authInit();
        $provider_id = trim( strip_tags( $this->request["hauth_done"] ) );
        $hauth = Auth::setup( $provider_id );

        if( ! $hauth ) {
            $hauth->adapter->setUserUnconnected();
            throw new Exception( "Invalid parameter! Please return to the login page and try again." );
        }

        try {
            $hauth->adapter->loginFinish();
        } catch( Exception $e ) {
            Error::setError( $e->getMessage(), $e->getCode(), $e->getTraceAsString(), $e->getPrevious());
            $hauth->adapter->setUserUnconnected();
        }

        $hauth->returnToCallbackUrl();
        die();
    }

    protected function authInit()
    {
        if ( ! $this->initDone) {
            $this->initDone = TRUE;

            try {
                if(!class_exists("Hybrid_Storage", false)){
                    require_once realpath(dirname(__FILE__)) . "/Storage.php";
                }
                $storage = new Storage();
                if ( ! $storage->config( "CONFIG" ) ) {
                    throw new Exception( "You cannot access this page directly." );
                }
                Auth::initialize( $storage->config( "CONFIG" ) );
            } catch ( Exception $e ){
                throw new Exception( "Oophs. Error!" );
            }
        }
    }
}
