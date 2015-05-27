<?php
namespace User\Hybrid;

class Error
{
    public static function setError( $message, $code = NULL, $trace = NULL, $previous = NULL )
    {
        Auth::storage()->set( "hauth_session.error.status"  , 1         );
        Auth::storage()->set( "hauth_session.error.message" , $message  );
        Auth::storage()->set( "hauth_session.error.code"    , $code     );
        Auth::storage()->set( "hauth_session.error.trace"   , $trace    );
        Auth::storage()->set( "hauth_session.error.previous", $previous );
    }

    public static function clearError()
    {
        Auth::storage()->delete( "hauth_session.error.status"   );
        Auth::storage()->delete( "hauth_session.error.message"  );
        Auth::storage()->delete( "hauth_session.error.code"     );
        Auth::storage()->delete( "hauth_session.error.trace"    );
        Auth::storage()->delete( "hauth_session.error.previous" );
    }

    public static function hasError()
    {
        return (bool) Auth::storage()->get( "hauth_session.error.status" );
    }

    public static function getErrorMessage()
    {
        return Auth::storage()->get( "hauth_session.error.message" );
    }

    public static function getErrorCode()
    {
        return Auth::storage()->get( "hauth_session.error.code" );
    }

    public static function getErrorTrace()
    {
        return Auth::storage()->get( "hauth_session.error.trace" );
    }

    public static function getErrorPrevious()
    {
        return Auth::storage()->get( "hauth_session.error.previous" );
    }
}
