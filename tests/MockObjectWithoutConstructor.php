<?php
namespace Tests;

class MockObjectWithoutConstructor implements Mock{

    public function run()
    {
        return get_class($this);
    }
}