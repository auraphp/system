<?php
namespace Aura\Framework;
interface DispatcherInterface
{
    public function exec($path, array $server = null);
}
