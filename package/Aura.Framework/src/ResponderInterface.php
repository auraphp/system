<?php
namespace Aura\Framework;
use Aura\Web\ResponseTransfer;
interface ResponderInterface
{
    public function exec(ResponseTransfer $transfer);
}
