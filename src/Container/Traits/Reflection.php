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
            $dependencies = $constructor->getParameters();
            $instances = [];
            if(count($dependencies) > 0){
                foreach ($dependencies as $dependency){
                    if(is_null($dependency->getType())){
                        array_push($instances, $dependency);
                    }else{
                        array_push($instances, $this->resolveClass($dependency->getType()->getName()));
                    }
                }
                return $reflector->newInstanceArgs($instances);
            }
        }
        return $reflector->newInstanceWithoutConstructor();
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