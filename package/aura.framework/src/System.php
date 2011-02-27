<?php
namespace aura\framework;
class System
{
    protected $root;
    
    public function __construct($root)
    {
        $this->root = $root;
    }
    
    protected function getSubPath($dir, $sub = null)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $dir;
        if ($sub) {
            $path .= DIRECTORY_SEPARATOR
                   . str_replace('/', DIRECTORY_SEPARATOR, $sub);
        }
        return $path;
    }
    
    public function getRootPath($sub = null)
    {
        $path = $this->root;
        if ($sub) {
            $path .= DIRECTORY_SEPARATOR
                   . str_replace('/', DIRECTORY_SEPARATOR, $sub);
        }
        return $path;
    }
    
    public function getPackagePath($sub = null)
    {
        return $this->getSubPath('package', $sub);
    }
    
    public function getTmpPath($sub = null)
    {
        return $this->getSubPath('tmp', $sub);
    }
    
    public function getConfigPath($sub = null)
    {
        return $this->getSubPath('config', $sub);
    }
    
    public function getPublicPath($sub = null)
    {
        return $this->getSubPath('public', $sub);
    }
    
    public function getIncludePath($sub = null)
    {
        return $this->getSubPath('include', $sub);
    }
}
