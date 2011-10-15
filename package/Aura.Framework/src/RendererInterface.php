<?php
namespace Aura\Framework;
use Aura\Web\ResponseTransfer;
interface RendererInterface
{
    public function exec(ResponseTransfer $transfer, array $accept = array());
}
