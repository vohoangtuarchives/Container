<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Tuezy\Container\Container;

class ContainerTest extends TestCase{

    public function testMockInit(){
        $container = Container::getInstance();
        $mockWithConstructor = $container->make(MockObjectWithConstructor::class);
        $mockWithoutConstructor = $container->make(MockObjectWithoutConstructor::class);
        $this->assertEquals(MockObjectWithConstructor::class, $mockWithConstructor->run());
        $this->assertEquals(MockObjectWithoutConstructor::class, $mockWithoutConstructor->run(), "Test init object witout constructor");
    }

    public function testAliasContainer(){
        $container = Container::getInstance();
        $container->alias('mock', MockObjectWithoutConstructor::class);
        $mock = $container->make('mock');
        $this->assertEquals(MockObjectWithoutConstructor::class, $mock->run());
    }

}