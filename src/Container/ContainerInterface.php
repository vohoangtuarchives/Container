<?php
namespace Tuezy\Container;

use ArrayAccess;
use Psr\Container\ContainerInterface as CI;
interface ContainerInterface extends CI, ArrayAccess{
    /**
     * @param $abstract
     * @param null $concrete
     * @return mixed
     */
    public function assign(string $abstract, $concrete = null);

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