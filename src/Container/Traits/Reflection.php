<?php
namespace Tuezy\Container\Traits;

trait Reflection{

    /**
     * store initialized object
     * @var array
     */
    protected $instances = [];

    private function resolve($abstract)
    {
        try {
            $abstract = $this->alias($abstract);
            if(isset($this->instances[$abstract])){
                return $this->instances[$abstract];
            }
            return $this->instances[$abstract] = $this->resolveClass($abstract);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return false;
    }

    private function resolveClass($abstract){
        $reflector = new \ReflectionClass($abstract);
        if (! $reflector->isInstantiable()) {
            throw new \Exception("$reflector is not instantiable");
        }
        $constructor = $reflector->getConstructor();
        if(!is_null($constructor)){
            return $this->resolveClassWithConstructor($constructor, $reflector);
        }
        return $this->resolveClassWithoutConstructor($reflector);
    }

    private function resolveClassWithConstructor($constructor, $reflector){
        $dependencies = $constructor->getParameters();
        if(count($dependencies) > 0){
            $instances = $this->resolveDependenies($dependencies);
            return $reflector->newInstanceArgs($instances);
        }
    }

    private function resolveClassWithoutConstructor($reflector){
        return $reflector->newInstanceWithoutConstructor();
    }

    private function resolveDependenies($dependencies){
        $instances = [];
        foreach ($dependencies as $dependency){
            if(is_null($dependency->getType())){
                array_push($instances, $dependency);
            }else{
                array_push($instances, $this->resolveClass($dependency->getType()->getName()));
            }
        }
        return $instances;
    }

    private function resolveMethod($abstract, $fn){
        $method = new \ReflectionMethod($abstract, $fn);
        $method->setAccessible(true);
        $parameters = $method->getParameters();
        $methodPara = [];
        foreach ($parameters as $parameter){
            $methodPara[] = $this->resolve($parameter->getType()->getName());
        }
        $controller = $this->resolve($abstract);
        return $method->invokeArgs($controller,$methodPara);
    }
}