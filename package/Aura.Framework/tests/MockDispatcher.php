<?php
namespace Aura\Framework;
use Aura\Web\ResponseTransfer;
class MockDispatcher implements DispatcherInterface
{
    public function exec($path, array $server = null)
    {
        return new ResponseTransfer;
    }
}
