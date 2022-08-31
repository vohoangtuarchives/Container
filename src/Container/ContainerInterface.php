<?php
namespace Tuezy\Container;

interface ContainerInterface{
    /**
     * @param $abstract
     * @param null $concrete
     * @return mixed
     */
    public function alias($abstract, $concrete = null);

    /**
     * @param $abstract
     * @param $concrete
     * @return mixed
     */
    public function instance($abstract, $concrete);

    /**
     * @param $abstract
     * @return mixed
     */
    public function make($abstract);

    /**
     * @return ContainerInterface
     */
    public static function getInstance() : ContainerInterface;
}