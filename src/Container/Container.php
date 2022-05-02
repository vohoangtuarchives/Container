<?php
namespace Tuezy\Container;

use Tuezy\Container\Traits\Reflection;
use Tuezy\Container\Exceptions\AlreadyExistsException;
use Tuezy\Container\Traits\Singleton;

class Container{
    use Singleton, Reflection;

    protected $bind = [];
    protected $alias = [];
    protected $lazyBind = [];

    function resolve($abstract)
    {
        try {
            $abstract = $this->alias($abstract);
            $reflector = new \ReflectionClass($abstract);
            if (! $reflector->isInstantiable()) {
                throw new \Exception("$reflector is not instantiable");
            }

            $constructor = $reflector->getConstructor();
            if(!is_null($constructor)){
                $dependencies = $constructor->getParameters();
                $instances = [];
                if(count($dependencies) > 0){
                    foreach ($dependencies as $dependency){
                        if(is_null($dependency->getType())){
                            array_push($instances, $dependency);
                        }else{
                            array_push($instances, $this->resolve($dependency->getType()->getName()));
                        }
                    }
                    return $reflector->newInstanceArgs($instances);
                }
            }
            return $reflector->newInstanceWithoutConstructor();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return false;
    }

    public function bind($abstract, $concrete){
        if(isset($this->bind[$abstract]))
            throw new AlreadyExistsException();
        if(is_string($concrete)){
            $concrete = $this->tryToResolve($concrete);
        }
        $this->bind[$abstract] = $concrete;
    }

    public function alias($abstract, $concrete = null){
        if(isset($this->alias[$abstract]) && !is_null($concrete))
            throw new AlreadyExistsException;
        if(is_string($concrete)){
            $this->alias[$abstract] = $concrete;
        }
        if(is_null($concrete) && isset($this->alias[$abstract])){
            return $this->alias[$abstract];
        }
        return $abstract;
    }

    public function bindLazy($abstract, $concrete){
        if(isset($this->lazyBind[$abstract]))
            throw new AlreadyExistsException;
        $this->lazyBind[$abstract] = $concrete;
    }

    public function make($abstract){
        $bindAbstract = $this->alreadyBind($abstract);
        if($bindAbstract){
            if ($bindAbstract[1] === 1) return $bindAbstract[0]; // Bind
            else{
                $this->bind($abstract, $bindAbstract[0]);
                return $this->bind[$abstract];
            }
        }
        return null;
    }
}