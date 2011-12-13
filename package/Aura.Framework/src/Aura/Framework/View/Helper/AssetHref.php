<?php
namespace Aura\Framework\View\Helper;
use Aura\View\Helper\AbstractHelper;
class AssetHref extends AbstractHelper
{
    public function setBase($base)
    {
        $this->base = rtrim($base, '/');
    }
    
    public function __invoke($href)
    {
        return $this->base . '/' . ltrim($href, '/');
    }
}
