<?php
namespace Tests;

class MockObjectWithConstructor implements Mock {

    protected $mock;

    public function __construct(MockObjectWithoutConstructor $mockObjectWithoutConstructor)
    {
        $this->mock = $mockObjectWithoutConstructor;
    }

    public function run(){
        return __CLASS__;
    }
}