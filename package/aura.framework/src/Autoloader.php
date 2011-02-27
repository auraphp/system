<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\framework;

/**
 * 
 * An SPL autoloader adhering to PSR-0.
 * 
 * @package aura.framework
 * 
 */
class Autoloader
{
    /**
     * 
     * Classes and interfaces loaded by the autoloader; the key is the class
     * name and the value is the file name.
     * 
     * @var array
     * 
     */
    protected $loaded = array();
    
    protected $paths = array();
    
    public function register($config_mode = null)
    {
        if ($config_mode == 'test') {
            spl_autoload_register(array($this, 'loadTests'));
        } else {
            spl_autoload_register(array($this, 'load'));
        }
    }
    
    /**
     * 
     * Adds a path for a class name prefix.
     * 
     * @param string $name The class name prefix, e.g. 'aura\framework\\' or
     * 'Zend_'.
     * 
     * @param string $path The absolute path leading to the classes for that
     * prefix, e.g. '/path/to/system/package/aura.framework-dev/src'.
     * 
     * @return void
     * 
     */
    public function setPath($name, $path)
    {
        $this->paths[$name] = rtrim($path, DIRECTORY_SEPARATOR);
    }
    
    /**
     * 
     * Returns the list of all class name prefixes and their paths.
     * 
     * @return array
     * 
     */
    public function getPaths()
    {
        return $this->paths;
    }
    
    /**
     * 
     * Loads a class or interface using the class name prefix and path,
     * falling back to the include-path if not found.
     * 
     * @param string $class The class or interface to load.
     * 
     * @return void
     * 
     * @throws RuntimeException when the file for the class or interface is
     * not found.
     * 
     */
    public function load($class)
    {
        // go through each of the registered paths
        foreach ($this->paths as $name => $path) {
            
            // get the length of the package name
            $len = strlen($name);
            
            // skip if the class prefix does not match
            if (substr($class, 0, $len) != $name) {
                continue;
            }
            
            // strip the name prefix from the class
            $spec = substr($class, $len);
            
            // convert the remaining spec to a file name
            $file = $path . DIRECTORY_SEPARATOR . $this->classToFile($spec);
            
            // try to load it
            if (! file_exists($file)) {
                throw new Exception_AutoloadFileNotFound($file);
            }
            $this->loadClassFile($class, $file);
            return;
        }
        
        // fall back to the include path
        $this->loadRealFile($class);
    }
    
    public function loadTests($class)
    {
        // go through each of the registered paths
        foreach ($this->paths as $name => $path) {
            
            // get the length of the package name
            $len = strlen($name);
            
            // skip if the class prefix does not match
            if (substr($class, 0, $len) != $name) {
                continue;
            }
            
            // strip the name prefix from the class
            $spec = substr($class, $len);
            
            // convert the remaining spec to a file name
            $file = $path . DIRECTORY_SEPARATOR . $this->classToFile($spec);
            
            // does it exist in the normal path?
            if (file_exists($file)) {
                $this->loadClassFile($class, $file);
                return;
            }
            
            // does it exist in /tests/ instead of /src/ ?
            $find = DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
            $repl = DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;
            $file = str_replace($find, $repl, $file);
            if (file_exists($file)) {
                $this->loadClassFile($class, $file);
                return;
            }
        }
        
        // fall back to the include path
        $this->loadRealFile($class);
    }
    
    /**
     * 
     * Returns the list of classes and interfaces loaded by the autoloader.
     * 
     * @return array An array of key-value pairs where the key is the class
     * or interface name and the value is the file name.
     * 
     */
    public function getLoaded()
    {
        return $this->loaded;
    }
    
    /**
     * 
     * PSR-0 compliant class-to-file inflector.
     * 
     * @param string $spec The name of the class or interface to load.
     * 
     * @return string The filename version of the class or interface.
     * 
     */
    public function classToFile($spec)
    {
        // look for last namespace separator
        $pos = strrpos($spec, '\\');
        if ($pos === false) {
            // no namespace, class portion only
            $namespace = '';
            $class     = $spec;
        } else {
            // pre-convert namespace portion to file path
            $namespace = substr($spec, 0, $pos);
            $namespace = str_replace('\\', DIRECTORY_SEPARATOR, $namespace)
                       . DIRECTORY_SEPARATOR;
        
            // class portion
            $class = substr($spec, $pos + 1);
        }
        
        // convert class underscores
        $file = $namespace
              . str_replace('_',  DIRECTORY_SEPARATOR, $class)
              . '.php';
        
        // done!
        return $file;
    }
    
    protected function loadClassFile($class, $file)
    {
        require $file;
        $this->loaded[$class] = $file;
    }
    
    protected function loadRealFile($class)
    {
        $file = $this->classToFile($class);
        try {
            $obj = new \SplFileObject($file, 'r', true);
        } catch (\RuntimeException $e) {
            throw new Exception_AutoloadFileNotFound($file);
        }
        $real = $obj->getRealPath();
        require $real;
        $this->loaded[$class] = $real;
    }
}
