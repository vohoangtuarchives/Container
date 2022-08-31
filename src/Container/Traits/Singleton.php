<?php
namespace Tuezy\Container\Traits;

use Tuezy\Container\Container;

trait Singleton{
    /**
     * @var
     */
    protected static $instance;

    /**
     * @return Container
     */
    public static function getInstance() : Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

}