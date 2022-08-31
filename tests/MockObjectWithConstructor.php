<?php
namespace Tests;

class MockObjectWithConstructor implements Mock {

    protected $mock;

    protected $request;

    public function __construct(MockObjectWithoutConstructor $mockObjectWithoutConstructor, $request)
    {
        $this->mock = $mockObjectWithoutConstructor;
        $this->request = $request;
    }

    public function run(){
        return __CLASS__;
    }
}