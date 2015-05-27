<?php
namespace User\Provider\Model;

abstract class Model
{
    public $providerId = NULL;
    public $config     = NULL;
    public $params     = NULL;
    public $endpoint   = NULL;
    public $user       = NULL;
    public $api        = NULL;

    function __construct( $providerId, $config, $params = NULL )
    {
        if( ! $params ){
            $this->params = \User\Hybrid\Auth::storage()->get( "hauth_session.$providerId.id_provider_params" );
        } else {
            $this->params = $params;
        }

        $this->providerId = $providerId;
        $this->endpoint = \User\Hybrid\Auth::storage()->get( "hauth_session.$providerId.hauth_endpoint" );
        $this->config = $config;
        $this->user = new \User\Hybrid\User();
        $this->user->providerId = $providerId;

        $this->initialize();
    }

    abstract protected function initialize();
    abstract protected function loginBegin();
    abstract protected function loginFinish();

    function logout()
    {
        $this->clearTokens();
        return TRUE;
    }

    function getUserProfile()
    {
        throw new \Exception( "Provider does not support this feature.", 8 );
    }

    function getUserContacts()
    {
        throw new \Exception( "Provider does not support this feature.", 8 );
    }

    function getUserActivity( $stream )
    {
        throw new \Exception( "Provider does not support this feature.", 8 );
    }

    function setUserStatus( $status )
    {
        throw new \Exception( "Provider does not support this feature.", 8 );
    }

    function getUserStatus( $statusid )
    {
        throw new \Exception( "Provider does not support this feature.", 8 );
    }

    public function isUserConnected()
    {
        return (bool) \User\Hybrid\Auth::storage()->get( "hauth_session.{$this->providerId}.is_logged_in" );
    }

    public function setUserConnected()
    {
        \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->providerId}.is_logged_in", 1 );
    }

    public function setUserUnconnected()
    {
        \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->providerId}.is_logged_in", 0 );
    }

    public function token( $token, $value = NULL )
    {
        if( $value === NULL ){
            return \User\Hybrid\Auth::storage()->get( "hauth_session.{$this->providerId}.token.$token" );
        }
        else{
            \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->providerId}.token.$token", $value );
        }
    }

    public function deleteToken( $token )
    {
        \User\Hybrid\Auth::storage()->delete( "hauth_session.{$this->providerId}.token.$token" );
    }

    public function clearTokens()
    {
        \User\Hybrid\Auth::storage()->deleteMatch( "hauth_session.{$this->providerId}." );
    }
}
