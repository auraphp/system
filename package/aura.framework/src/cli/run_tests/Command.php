<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli\run_tests;
use Aura\Cli\Command as CliCommand;
use Aura\Cli\Getopt;
use Aura\Cli\Option;
use Aura\Framework\System;
use Aura\Framework\Exception\TestFileNotFound;

/**
 * 
 * This command uses PHPUnit to run package tests.
 * 
 * Usage to run all tests:
 * 
 *      $ php cli.php aura.framework.run-tests
 * 
 * No command-line options will be honored.
 * 
 * Usage to run tests in a single package:
 * 
 *      $ php cli.php aura.framework.run-tests {$PATH}
 * 
 * ... where `$PATH` is the path to a package test directory, e.g.
 * `package/aura.framework/tests/`.  All options and switches passed at
 * the command line will be passed to PHPUnit.
 * 
 * @package aura.framework
 * 
 */
class Command extends CliCommand
{
    /**
     * 
     * Put Getopt into non-strict mode so that we don't need to redefine
     * PHPUnit options here.
     * 
     * @var bool
     * 
     */
    protected $options_strict = Getopt::NON_STRICT;
    
    protected $options = array(
        'exclude_package' => array(
            'long' => 'exclude-package',
            'multi' => true,
            'param' => Option::PARAM_REQUIRED,
        ),
    );
    
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
     * @var System
     * 
     */
    protected $system;
    
    /**
     * 
     * Sets the System object.
     * 
     * @param System $system The System object.
     * 
     * @return void
     * 
     */
    public function setSystem(System $system)
    {
        $this->system = $system;
    }
    
    /**
     * 
     * Sets the PHPUnit command.
     * 
     * @param string $phpunit The PHPUnit command.
     * 
     * @return void
     * 
     */
    public function setPhpunit($phpunit)
    {
        $this->phpunit = $phpunit;
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
        $system_dir = $this->system->getRootPath();
        $proc = proc_open($cmd, $spec, $pipes, $system_dir);
        proc_close($proc);
    }
    
    /**
     * 
     * Builds the command to run PHPUnit for one test series.
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
            throw new TestFileNotFound($file);
        }
        
        // start building the phpunit command
        $cmd = array($this->phpunit);
        
        // add bootstrap file
        $subpath   = 'aura.framework/scripts/test-bootstrap.php';
        $bootstrap = $this->system->getPackagePath($subpath);
        $cmd[]     = '--bootstrap=' . escapeshellarg($bootstrap);
        
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
            $coverage_dir = $this->system->getTmpPath('test/coverage');
            $cmd[]        = " --coverage-html=$coverage_dir";
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
        
        $subpath   = 'aura.framework/scripts/test-bootstrap.php';
        $bootstrap = $this->system->getPackagePath($subpath);
        $xml[] = "<phpunit bootstrap=\"{$bootstrap}\">";
        
        $xml[] = '<testsuites>';
        
        $exclude = $this->getopt->exclude_package;
        
        $package_dir  = $this->system->getPackagePath();
        $package_glob = $package_dir . DIRECTORY_SEPARATOR . '*';
        $package_list = glob($package_glob, GLOB_ONLYDIR);
        foreach ($package_list as $package_base) {
            $package_name = basename($package_base);
            if (in_array($package_name, $exclude)) {
                continue;
            }
            $package_test = $package_base . DIRECTORY_SEPARATOR . 'tests';
            if (is_dir($package_test)) {
              $xml[] = "<testsuite name=\"{$package_name}\">";
              $xml[] = "<directory>{$package_test}</directory>";
              $xml[] = '</testsuite>';
            }
        }
        
        $xml[] = '</testsuites>';
        
        $xml[] = '</phpunit>';
        
        $tmp_dir = $this->system->getTmpPath();
        $file = $tmp_dir . DIRECTORY_SEPARATOR
              . 'test' . DIRECTORY_SEPARATOR
              . 'phpunit.xml';
        
        @mkdir(dirname($file), 0777, true);
        
        file_put_contents($file, implode(PHP_EOL, $xml));
        
        return $file;
    }
}
