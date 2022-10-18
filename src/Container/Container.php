<?php
namespace Tuezy\Container;

use Tuezy\Container\Exceptions\NotFoundException;
use Tuezy\Container\Exceptions\AlreadyExistsException;
use Tuezy\Container\Traits\Reflection;

class Container implements ContainerInterface{
    use Reflection;

    protected $items = [];

    protected $alias = [];

    protected $shared = [];

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
        if(isset($this->instances[$id])){
            return $this->instances[$id];
        }

        if($this->has($id)){
            $item = $this->items[$id];
            if(is_string($item) && class_exists($item)){
                return $this->resolve($item);
            }
            if(is_callable($item)){
                $object = call_user_func($item, $this);
                //replace callable closure with object had resolve
                return $this->assign($id, $object);
            }

            return $item;
        }
        else
            throw new NotFoundException("{$id} not existed.");
    }

    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function assign(string $abstract, $concrete = null, $shared = false)
    {
        if($shared == false && $this->has($abstract))
            throw new AlreadyExistsException("{$abstract} defined!");
        if($shared == true){
            $this->shared[$abstract] = true;
            $this->items[$abstract][] = $concrete;
            $this->items[$abstract] = array_unique($this->items[$abstract]);
        }else{
            $this->items[$abstract] = $concrete;
        }

    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function make($abstract)
    {
        $abstract = $this->get($abstract);
        return $this->resolve($abstract);
    }

    public function alias($abstract, $concrete = null){
        if(!is_string($abstract)) throw new \Exception('$abstract must be string in alias function');
        if($this->aliased($abstract)){
            if(is_null($concrete)) return $this->alias[$abstract];
            else throw new \Exception('$abstract existed');
        }else{
            if(is_null($concrete)) throw new \Exception('you forget to provide $concerete');
            $this->alias[$abstract] = $concrete;
        }
        return null;
    }

    public function aliased($abstract){
        return isset($this->alias[$abstract]);
    }
}
