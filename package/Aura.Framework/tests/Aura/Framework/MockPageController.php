<?php
namespace Aura\Framework;
class MockPageController {
    public function __construct()
    {
        // do nothing
    }
    
    public function exec()
    {
        return __METHOD__;
    }
}
