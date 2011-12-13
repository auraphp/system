<?php
namespace Aura\Framework;
use Aura\Web\ResponseTransfer;
class MockResponder implements ResponderInterface
{
    public function exec(ResponseTransfer $transfer)
    {
        return __METHOD__;
    }
}
