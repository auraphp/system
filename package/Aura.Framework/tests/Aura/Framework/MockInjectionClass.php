<?php
namespace Aura\Framework;
class MockInjectionClass extends MockEmptyClass {
    
    protected $mock_empty;
    
    public function __construct($foo, \Aura\Framework\MockEmptyClass $mock_empty)
    {
        parent::__construct($foo);
        $this->mock_empty = $mock_empty;
    }
}
