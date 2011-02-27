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
use aura\cli\Context as Context;
use aura\cli\Stdio as Stdio;
use aura\cli\Getopt as Getopt;
use aura\framework\Inflect as Inflect;
use aura\framework\System as System;

/**
 * 
 * This CLI command uses PHPUnit to make a skeleton test file from an existing 
 * class.
 * 
 * @package aura.test
 * 
 */
class Make extends Controller
{
    
    protected $include_path;
    
    /**
     * 
     * A word inflector.
     * 
     * @var aura\framework\Inflect
     * 
     */
    protected $inflect;
    
    /**
     * 
     * The directory where packages reside.
     * 
     * @var string
     * 
     */
    protected $package_dir;
    
    /**
     * 
     * The directory where test classes should be created.
     * 
     * @var string
     * 
     */
    protected $test_dir;
    
    public function setSystem(System $system)
    {
        $this->system = $system;
    }
    
    public function setInflect(Inflect $inflect)
    {
        $this->inflect = $inflect;
    }
    
    public function preAction()
    {
        $this->include_path = ini_get('include_path');
        $newpath = $this->include_path
                 . PATH_SEPARATOR
                 . dirname(__DIR__) . DIRECTORY_SEPARATOR
                 . 'PHPUnit-3.4.15';
        ini_set('include_path', $newpath);
    }
    
    public function postAction()
    {
        ini_set('include_path', $this->include_path);
    }
    
    /**
     * 
     * Creates a test file from an existing class.
     * 
     * @param string $spec The class file. E.g.,
     * `package/aura.framework/src/Factory.php`, not `aura\\core\\Factory`.
     * 
     * @return void
     * 
     */
    public function action()
    {
        $spec = $this->params[0];
        
        list($vendor, $package, $class) = $this->getVendorPackageClass($spec);
        
        // the fully-qualified class to write a test from
        $incl_name = "{$vendor}\\{$package}\\$class";
        
        // the *class name only* of the test to write
        $test_name = "{$class}Test";
        
        // find the original source file:
        // include/$class_to_file
        $incl_file = $spec;
        $this->stdio->outln("Source file is '$incl_file'.");
        
        // look where the test file will go:
        // package/$vendor.$package/tests/$class_to_file
        $test_file = $this->system->getPackagePath(
            "{$vendor}.{$package}/tests/" . $this->inflect->classToFile($test_name)
        );
        
        if (is_file($test_file)) {
            throw new Exception_TestFileExists(array(
                'file' => $test_file,
            ));
        }
        
        // generate the skeleton code
        $skel = new \PHPUnit_Util_Skeleton_Test(
            $incl_name,
            $incl_file,
            $test_name,
            $test_file
        );
        
        $skel_code = $skel->generate();
        
        // modify the resulting code
        $test_code = $this->modifySkeleton($skel_code, "$vendor\\$package");
        
        // make sure a directory exists for the test file
        $test_dir = dirname($test_file);
        @mkdir($test_dir, 0755, true);
        
        // write the test file
        file_put_contents($test_file, $test_code);
        $this->stdio->outln("Test file created at '$test_file'.");
    }
    
    /**
     * 
     * Given a class specification, extract the vendor, package, and class 
     * names.
     * 
     * @param string $spec The fully-qualified class specification.
     * 
     * @return array A seqential array of vendor name, package name, and
     * class name.
     * 
     */
    protected function getVendorPackageClass($spec)
    {
        // incoming spec: packages/aura.framework/src/foo/Bar.php
        $real = realpath($spec);
        if (! $real) {
            throw new Exception_SourceNotFound(array(
                'file' => $spec,
            ));
        }
        
        // strip off the package dir prefix
        $len  = strlen($this->system->getPackagePath() . DIRECTORY_SEPARATOR);
        $spec = substr($real, $len);
        
        // this should leave us with, e.g., aura.framework/src/foo/Bar.php
        // get the package name out
        $part = explode(DIRECTORY_SEPARATOR, $spec);
        
        // pull off the top part and turn into vendor and package
        list($vendor, $package) = explode('.', array_shift($part));
        
        // pull off 'src'
        array_shift($part);
        
        // turn the rest into a class, minus .php
        $class = substr(implode('\\', $part), 0, -4);
        
        return array($vendor, $package, $class);
    }
    
    /**
     * 
     * Given a test class skeleton from PHPUnit, modify it so that it works
     * nicely within the Aura testing system.
     * 
     * @param string $skel The PHPUnit test class skeleton.
     * 
     * @param string $namespace The namespace of the class being tested.
     * 
     * @return string The modified test skeleton.
     * 
     */
    protected function modifySkeleton($skel, $namespace)
    {
        $skel = str_replace(
            "<?php\n",
            "<?php\nnamespace $namespace;\n\n",
            $skel
        );
        
        $skel = preg_replace('/\nrequire_once.*\n/', '', $skel);
        
        $skel = str_replace(
            'extends PHPUnit_Framework_TestCase',
            'extends \PHPUnit_Framework_TestCase',
            $skel
        );
        
        $skel = str_replace(
            "function setUp()\n    {",
            "function setUp()\n    {\n        parent::setUp();",
            $skel
        );
        
        $skel = str_replace(
            "function tearDown()\n    {",
            "function tearDown()\n    {\n        parent::tearDown();",
            $skel
        );
        
        $skel = preg_replace('/\?\>\n$/', '', $skel);
        
        return $skel;
    }
}
