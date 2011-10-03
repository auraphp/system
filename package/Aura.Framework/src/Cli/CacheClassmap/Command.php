<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli\CacheClassmap;
use Aura\Cli\Command as CliCommand;
use Aura\Framework\System as System;

/**
 * 
 * Caches all package source class files.
 * 
 * @package Aura.Framework
 * 
 */
class Command extends CliCommand
{
    protected $system;
    
    public function setSystem(System $system)
    {
        $this->system = $system;
    }
    
    public function action()
    {
        $this->stdio->outln("Caching package class map ... ");
        
        // the eventual class map
        $classmap = array();
        
        // get the list of all packages in the system
        $package_glob = $this->system->getPackagePath('*');
        $package_list = glob($package_glob, GLOB_ONLYDIR);
        
        // go through each package in the system ...
        foreach ($package_list as $package_dir) {
            
            // package class prefix
            $prefix = str_replace('.', '\\', basename($package_dir));
            
            // ... and iterate over its src dir
            $path = $package_dir . DIRECTORY_SEPARATOR . 'src';
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            
            $skip = strlen($path) + 1;
            foreach ($items as $file => $object) {
                $base = substr($file, $skip);
                if (preg_match('/^[A-Z].*\.php$/', $base)) {
                    $base = substr($base, 0, -4);
                    $base = str_replace(DIRECTORY_SEPARATOR, '\\', $base);
                    $class = $prefix . '\\' . $base;
                    $classmap[$class] = $file;
                    $this->stdio->outln($class . ' => ' . $file);
                }
            }
        }
        
        // create and/or clear the cached classmap.php file
        $cache = $this->system->getTmpPath("cache/classmap.php");
        @mkdir(dirname($cache), 0777, true);
        $output = "<?php return " . var_export($classmap, true) . ';';
        file_put_contents($cache, $output);
        $this->stdio->outln("Cached to $cache.");
        $this->stdio->outln('Done.');
    }
}
