<?php
namespace aura\framework;
class MockUnifiedClass extends Unified {
    
    protected $__construct = array(
        'foo' => 'bar',
        'mock_empty' => null,
        'no_property' => 'stays_in_construct',
    );
    
    protected $__injection = array(
        'mock_empty' => 'aura\framework\MockEmptyClass',
    );
    
    protected $foo;
    
    protected $mock_empty;
    
    protected $pre_config = false;
    
    protected $post_config = false;
    
    protected $post_construct = false;
    
    protected function preConfig()
    {
        parent::preConfig();
        $this->pre_config = true;
    }
    
    protected function postConfig()
    {
        parent::postConfig();
        $this->post_config = true;
    }
    
    protected function postConstruct()
    {
        parent::postConstruct();
        $this->post_construct = true;
    }
    
    public function getPreConfig()
    {
        return $this->pre_config;
    }
    
    public function getPostConfig()
    {
        return $this->post_config;
    }
    
    public function getPostConstruct()
    {
        return $this->post_construct;
    }
    
    public function getFoo()
    {
        return $this->foo;
    }
    
    public function getConstruct()
    {
        return $this->__construct;
    }
    
    public function getInjection()
    {
        return $this->__injection;
    }
    
    public function getMockEmpty()
    {
        return $this->mock_empty;
    }
}
