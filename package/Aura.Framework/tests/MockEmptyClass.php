<?php
namespace Aura\Framework;
class MockEmptyClass {
    
    protected $foo;
    
    public function __construct($foo = 'bar')
    {
        $this->foo = $foo;
    }
    
    public function getFoo()
    {
        return $this->foo;
    }
}
