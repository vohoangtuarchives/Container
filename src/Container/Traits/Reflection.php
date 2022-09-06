<?php
namespace Tuezy\Container\Traits;

trait Reflection{

    /**
     * store initialized object
     * @var array
     */
    protected $instances = [];

    /**
     * @param $abstract
     * @return mixed|string|void
     */
    private function resolve($abstract)
    {
        return $this->instances[$abstract] = $this->resolveClass($abstract);
    }

    /**
     * @param $abstract
     * @return mixed|void
     * @throws \ReflectionException
     */
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

    /**
     * @param $constructor
     * @param $reflector
     * @return mixed|void
     */
    private function resolveClassWithConstructor($constructor, $reflector){
        $dependencies = $constructor->getParameters();
        if(count($dependencies) > 0){
            $instances = $this->resolveDependenies($dependencies);
            return $reflector->newInstanceArgs($instances);
        }
    }

    /**
     * @param $reflector
     * @return mixed
     */
    private function resolveClassWithoutConstructor($reflector){
        return $reflector->newInstanceWithoutConstructor();
    }

    /**
     * @param $dependencies
     * @return array
     * @throws \ReflectionException
     */
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

    /**
     * @param $abstract
     * @param $fn
     * @return mixed
     * @throws \ReflectionException
     */
    private function resolveMethod($abstract, $fn){
        $method = new \ReflectionMethod($abstract, $fn);
        $method->setAccessible(true);
        $parameters = $method->getParameters();
        $methodPara = [];
        foreach ($parameters as $parameter){
            $methodPara[] = !is_null($parameter->getType())
                ? $this->resolve($parameter->getType()->getName())
                : $parameter->getDefaultValue();
        }
        $controller = $this->resolve($abstract);
        return $method->invokeArgs($controller,$methodPara);
    }
}