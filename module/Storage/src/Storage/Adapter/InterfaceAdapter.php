<?php
namespace Storage\Adapter;

interface InterfaceAdapter
{
    public function remove($path);
    public function map($path);
    public function href($path);
    public function create($path, $extension);
}