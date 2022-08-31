<?php
namespace Tuezy\Container;

interface ContainerInterface{
    public function alias($abstract, $concrete = null);
    public function instance($abstract, $concrete);
    public function make($abstract);
    public static function getInstance() : ContainerInterface;
}