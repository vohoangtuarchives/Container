<?php
namespace Tuezy\Container;

use Tuezy\Container\Exceptions\NotFoundException;
use Tuezy\Container\Exceptions\AlreadyExistsException;
use Tuezy\Container\Traits\Reflection;

class Container implements ContainerInterface{
    use Reflection;

    protected $items = [];

    protected static $instance;

    public static function getInstance() : Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function get(string $id)
    {
        if($this->has($id))
            return $this->items[$id];
        else
            throw new NotFoundException("{$id} not existed.");
    }

    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function assign(string $abstract, $concrete = null)
    {
        if($this->has($abstract))
            throw new AlreadyExistsException("{$abstract} defined!");
       $this->items[$abstract] = $concrete;
    }

    public function offsetExists(mixed $offset)
    {
       return $this->has($offset);
    }

    public function offsetGet(mixed $offset)
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset(mixed $offset)
    {
        unset($this->items[$offset]);
    }

    public function make($abstract)
    {
        $abstract = $this->get($abstract);
        return $this->resolve($abstract);
    }
}
