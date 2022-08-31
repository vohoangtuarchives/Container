<?php
namespace Tuezy\Container;

use Tuezy\Container\Traits\Reflection;
use Tuezy\Container\Exceptions\AlreadyExistsException;
use Tuezy\Container\Traits\Singleton;

class Container{
    use Singleton, Reflection;

    /** store class for later resolve
     * @var array
     */
    protected $alias = [];

    /**
     * @param $abstract
     * @param $concrete
     * @return mixed|string
     * @throws AlreadyExistsException
     */
    public function alias($abstract, $concrete = null){
        if(isset($this->alias[$abstract]) && !is_null($concrete))
            throw new AlreadyExistsException;
        if(is_string($concrete)){
            $this->alias[$abstract] = $concrete;
        }
        if(is_null($concrete) && isset($this->alias[$abstract])){
            return $this->alias[$abstract]; //bind for later
        }
        return $abstract; //return raw abstract
    }

    public function instance($abstract, $concrete){
        if(is_string($abstract))
            return $this->alias($abstract, $concrete);
        return false;
    }

    /**
     * @param $abstract
     * @param $fn
     * @return mixed|object|string|null
     */
    public function make($abstract, $fn = null){
        if(is_null($fn)){
            return $this->resolve($abstract);
        }else{
            return $this->resolveMethod($abstract, $fn);
        }
    }

}
