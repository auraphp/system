<?php
namespace aura\framework;
class MockInjectionClass extends MockEmptyClass {
    
    protected $mock_empty;
    
    public function __construct($foo, \aura\framework\MockEmptyClass $mock_empty)
    {
        parent::__construct($foo);
        $this->mock_empty = $mock_empty;
    }
}
