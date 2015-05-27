<?php
namespace User\Hybrid;

interface StorageInterface
{
    public function config($key, $value = null);
    public function get($key);
    public function set( $key, $value );
    function clear();
    function delete($key);
    function deleteMatch($key);
    function getSessionData();
    function restoreSessionData( $sessiondata = null);
}
