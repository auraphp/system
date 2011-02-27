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
 * A generic Aura exception.
 * 
 * @package aura.framework
 * 
 * @todo Define default message template, and interpolate info through it.
 * 
 */
class Exception extends \Exception
{
    /**
     * 
     * User-defined information array.
     * 
     * @var array
     * 
     */
    protected $info = array();
    
    /**
     * 
     * Constructor.
     * 
     * @param array $info Key-value pairs of information about the exception.
     * 
     * @param \Exception $previous The previous exception, if any.
     * 
     */
    public function __construct($info = array(), \Exception $previous = null)
    {
        $this->info = (array) $info;
        $class = get_class($this);
        $pos = strrpos($class, '\\');
        $package = substr($class, 0, $pos);
        $class = substr($class, $pos + 1);
        if ($class == 'Exception') {
            $message = "Generic $package exception";
        } else {
            $message = "Package $package: $class";
        }
        parent::__construct($message, null, $previous);
    }
    
    /**
     * 
     * Returns the exception as a string.
     * 
     * @return void
     * 
     */
    public function __toString()
    {
        // basic string
        $str = "exception '" . get_class($this) . "'\n"
             . "with message '" . $this->message . "' \n"
             . "information " . var_export($this->info, true) . " \n"
             . "Stack trace:\n"
             . "  " . str_replace("\n", "\n  ", $this->getTraceAsString());
        
        // at the CLI, repeat the message so it shows up as the last line
        // of output, not the trace.
        if (PHP_SAPI == 'cli') {
            $str .= "\n\n{$this->message}";
        }
        
        // done
        return $str;
    }
    
    /**
     * 
     * Returns user-defined information.
     * 
     * @param string $key A particular info key to return; if empty, returns
     * all info.
     * 
     * @return array
     * 
     */
    public function getInfo($key = null)
    {
        if (empty($key)) {
            return $this->info;
        } else {
            return $this->info[$key];
        }
    }
}
