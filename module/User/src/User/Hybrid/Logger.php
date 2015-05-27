<?php
namespace User\Hybrid;

class Logger
{
    function __construct()
    {
        if ( Auth::$config["debug_mode"] ){
            if ( ! isset(Auth::$config["debug_file"]) ) {
                throw new Exception( "'debug_mode' is set to 'true' but no log file path 'debug_file' is set.", 1 );
            }
            elseif ( ! file_exists( Auth::$config["debug_file"] ) && ! is_writable( Auth::$config["debug_file"]) ){
                if ( ! touch( Auth::$config["debug_file"] ) ){
                    throw new Exception( "'debug_mode' is set to 'true', but the file " . Auth::$config['debug_file'] . " in 'debug_file' can not be created.", 1 );
                }
            }
            elseif ( ! is_writable( Auth::$config["debug_file"] ) ){
                throw new Exception( "'debug_mode' is set to 'true', but the given log file path 'debug_file' is not a writable file.", 1 );
            }
        }
    }

    public static function debug( $message, $object = NULL )
    {
        if( Auth::$config["debug_mode"] === true ){
            $datetime = new DateTime();
            $datetime =  $datetime->format(DATE_ATOM);

            file_put_contents(
                Auth::$config["debug_file"],
                "DEBUG -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . " -- " . print_r($object, true) . "\n",
                FILE_APPEND
            );
        }
    }

    public static function info( $message )
    {
        if( in_array(Auth::$config["debug_mode"], array(true, 'info'), true) ){
            $datetime = new DateTime();
            $datetime =  $datetime->format(DATE_ATOM);

            file_put_contents(
                Auth::$config["debug_file"],
                "INFO -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . "\n",
                FILE_APPEND
            );
        }
    }

    public static function error($message, $object = NULL)
    {
        if(isset(Auth::$config["debug_mode"]) && in_array(Auth::$config["debug_mode"], array(true, 'info', 'error'), true) ){
            $datetime = new DateTime();
            $datetime =  $datetime->format(DATE_ATOM);

            file_put_contents(
                Auth::$config["debug_file"],
                "ERROR -- " . $_SERVER['REMOTE_ADDR'] . " -- " . $datetime . " -- " . $message . " -- " . print_r($object, true) . "\n",
                FILE_APPEND
            );
        }
    }
}
