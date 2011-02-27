<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\framework;
use aura\cli\Controller as Controller;
use aura\cli\Getopt as Getopt;

/**
 * 
 * This CLI command uses PHPUnit to run the test suite for a package.
 * 
 * @package aura.test
 * 
 */
class RunTests extends Controller
{
    protected $options_strict = Getopt::NON_STRICT;
    
    /**
     * 
     * The command to run PHPUnit.
     * 
     * @var string
     * 
     */
    protected $phpunit;
    
    /**
     * 
     * The Aura system directory object.
     * 
     * @var string
     * 
     */
    protected $system;
    
    protected $package_dir;
    
    protected $tmp_dir;
    
    public function setSystem(\aura\framework\System $system)
    {
        $this->system = $system;
    }
    
    public function setPhpunit($phpunit)
    {
        $this->phpunit = $phpunit;
    }
    
    public function preAction()
    {
        parent::preAction();
        $this->system_dir = $this->system->getRootPath();
        $this->package_dir = $this->system->getPackagePath();
        $this->tmp_dir = $this->system->getTmpPath();
    }
    
    /**
     * 
     * Runs the specified PHPUnit test suite.
     * 
     * @return void
     * 
     */
    public function action()
    {
        if (empty($this->params)) {
            $cmd = $this->buildCmdAll();
        } else {
            $cmd = $this->buildCmdOne();
        }
        
        // build a pipe specification for proc_open
        $spec = array(
            $this->stdio->getStdin(),
            $this->stdio->getStdout(),
            $this->stdio->getStderr()
        );
        
        // run phpunit as a separate process
        $proc = proc_open($cmd, $spec, $pipes, $this->system_dir);
        proc_close($proc);
    }
    
    /**
     * 
     * 
     * @param string $cmd The command to run PHPUnit.
     * 
     * @return void
     * 
     */
    protected function runCmd($cmd)
    {
    }
    
    /**
     * 
     * Builds the command to run PHPUnit for one test series.
     * 
     * @param array $argv The command-line arguments, including options
     * for PHPUnit.
     * 
     * @return void
     * 
     */
    protected function buildCmdOne()
    {
        // go back to the original arguments
        $argv = $this->context->getArgv();
        
        // take the test file name off the top
        $file = array_shift($argv);
        
        // does the file or dir exist?
        $real = realpath($file);
        if (! file_exists($real)) {
            throw new Exception_TestFileNotFound(array(
                'test' => $file,
            ));
        }
        
        // start building the phpunit command
        $cmd = array($this->phpunit);
        
        // add bootstrap file
        $bootstrap = dirname(__DIR__) . DIRECTORY_SEPARATOR
                   . 'scripts' . DIRECTORY_SEPARATOR
                   . 'test-bootstrap.php';
        
        $cmd[] = '--bootstrap=' . escapeshellarg($bootstrap);
        
        // add all remaining args
        foreach ($argv as $val) {
            $cmd[] = escapeshellarg($val);
        }
        
        // add the real path to the test at the end
        $cmd[] = escapeshellarg($real);
        
        // build out the command and run it
        return implode(' ', $cmd);
    }
    
    /**
     * 
     * Builds the command to run PHPUnit for all tests in all packages.
     * 
     * @return void
     * 
     */
    protected function buildCmdAll()
    {
        // build the xml file
        $file = $this->writeXmlFile();
        
        // start building the phpunit command
        $cmd = array($this->phpunit);
        
        // add the xml file to the command
        $cmd[] = '--configuration=' . escapeshellarg($file);
        
        // add coverage
        if (extension_loaded('xdebug')) {
            $coverage_dir = $this->system_dir . DIRECTORY_SEPARATOR
                          . 'tmp' . DIRECTORY_SEPARATOR
                          . 'test' . DIRECTORY_SEPARATOR
                          . 'coverage';
            
            $cmd[] = " --coverage-html=$coverage_dir";
        }
        
        // build out the command and run it
        return implode(' ', $cmd);
    }
    
    /**
     * 
     * Writes out a "phpunit.xml" configuration file for running all tests.
     * 
     * @return string The path to the XML file.
     * 
     */
    protected function writeXmlFile()
    {
        $xml = array();
        
        $bootstrap = dirname(__DIR__) . DIRECTORY_SEPARATOR
                   . 'scripts' . DIRECTORY_SEPARATOR
                   . 'test-bootstrap.php';
                   
        $xml[] = "<phpunit bootstrap=\"{$bootstrap}\">";
        
        $xml[] = '<testsuites>';
        
        $package_glob = $this->package_dir . DIRECTORY_SEPARATOR . '*';
        $package_list = glob($package_glob, GLOB_ONLYDIR);
        foreach ($package_list as $package_base) {
            $package_name = basename($package_base);
            $package_test = $package_base . DIRECTORY_SEPARATOR . 'tests';
            $xml[] = "<testsuite name=\"{$package_name}\">";
            $xml[] = "<directory>{$package_test}</directory>";
            $xml[] = '</testsuite>';
        }
        
        $xml[] = '</testsuites>';
        
        $xml[] = '</phpunit>';
        
        $file = $this->tmp_dir . DIRECTORY_SEPARATOR
              . 'test' . DIRECTORY_SEPARATOR
              . 'phpunit.xml';
        
        @mkdir(dirname($file), 0777, true);
        
        file_put_contents($file, implode(PHP_EOL, $xml));
        
        return $file;
    }
}
