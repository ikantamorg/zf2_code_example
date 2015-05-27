<?php
namespace Storage\Adapter;

use Core\Traits\ServiceTraits;

class AbstractAdapter
{
    use ServiceTraits;


    protected $option_form = "";
    protected $type = '';
    protected $name = "";


    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFormOption($name = null, $options = [])
    {
        return new $this->option_form($name, $options);
    }

    protected function genName($extension)
    {
        $path = '';
        $base  = 255;
        $tmp = str_replace('.', '', microtime());
        do {
            $mod = ( $tmp % $base );
            $tmp -= $mod;
            $tmp /= $base;
            $path .= sprintf("%02x", $mod) . '/';
        } while( $tmp > 0 );

        $path .= time();
        return $path . '.' . $extension;
    }

}