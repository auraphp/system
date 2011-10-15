<?php
namespace Aura\Framework;
class MockMissingController {
    public function __construct()
    {
        // do nothing
    }
    
    public function exec()
    {
        return __METHOD__;
    }
}
