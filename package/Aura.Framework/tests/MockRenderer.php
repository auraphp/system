<?php
namespace Aura\Framework;
use Aura\Web\ResponseTransfer;
class MockRenderer implements RendererInterface
{
    public function exec(ResponseTransfer $transfer, array $accept = array())
    {
        // do nothing
    }
}
