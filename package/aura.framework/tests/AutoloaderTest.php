<?php
namespace aura\framework;

/**
 * Test class for Autoloader.
 */
class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testRegister()
    {
        $autoloader = new Autoloader;
        $autoloader->register();
        $functions = spl_autoload_functions();
        list($object, $method) = array_pop($functions);
        $this->assertType('aura\framework\Autoloader', $object);
        $this->assertSame('load', $method);
    }
    
    /**
     */
    public function testLoadAndLoaded()
    {
        $class = 'aura\framework\MockAutoloadClass';
        $autoloader = new Autoloader;
        $autoloader->setPath('aura\framework\\', __DIR__);
        $autoloader->load($class);
        
        $classes = get_declared_classes();
        $actual = array_pop($classes);
        $this->assertSame($class, $actual);
        
        $expect = array(
            $class => __DIR__ . DIRECTORY_SEPARATOR . 'MockAutoloadClass.php',
        );
        
        $actual = $autoloader->getLoaded();
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @expectedException \aura\framework\Exception_AutoloadFileNotFound
     */
    public function testLoadMissing()
    {
        $autoloader = new Autoloader;
        $autoloader->setPath('aura\framework\\', __DIR__);
        $autoloader->load('aura\framework\NoSuchClass');
    }
    
    /**
     * @expectedException \aura\framework\Exception_AutoloadFileNotFound
     */
    public function testLoadNotInIncludePath()
    {
        $autoloader = new Autoloader;
        $autoloader->load('NoSuchClass');
    }
    
    /**
     */
    public function testLoad_classWithoutNamespace()
    {
        // set a temp directory
        $dir = AURA_TEST_RUN_SYSTEM_DIR . DIRECTORY_SEPARATOR
             . 'tmp' . DIRECTORY_SEPARATOR
             . 'test' . DIRECTORY_SEPARATOR 
             . 'aura.framework.Autoloader';
        
        @mkdir($dir, 0777, true);
        
        // add to the include path *just for this test*
        $old_include_path = ini_get('include_path');
        ini_set('include_path', $old_include_path . PATH_SEPARATOR . $dir);
        
        // write a test file to the temp location
        $code = "<?php class ClassWithoutNamespace {}";
        $name = "$dir/ClassWithoutNamespace.php";
        file_put_contents($name, $code);
        
        // autoload it
        $expect = 'ClassWithoutNamespace';
        $autoloader = new Autoloader;
        $autoloader->setPath('aura\framework\\', __DIR__);
        
        $autoloader->load($expect);
        $classes = get_declared_classes();
        $actual = array_pop($classes);
        $this->assertSame($expect, $actual);
        
        // delete the file and directory
        unlink($name);
        rmdir($dir);
        
        // reset to old include path
        ini_set('include_path', $old_include_path);
    }
    
    public function testSetPathAndGetPaths()
    {
        $autoloader = new Autoloader;
        $autoloader->setPath('Foo_', '/path/to/Foo');
        $actual = $autoloader->getPaths();
        $expect = array('Foo_' => '/path/to/Foo');
        $this->assertSame($expect, $actual);
    }
    
    public function testClassToFile()
    {
        $autoloader = new Autoloader;
        
        $list = array(
            'Foo'                       => 'Foo.php',
            'Foo_Bar'                   => 'Foo/Bar.php',
            'foo\\Bar'                  => 'foo/Bar.php',
            'foo_bar\\Baz'              => 'foo_bar/Baz.php',
            'foo_bar\\Baz_Dib'          => 'foo_bar/Baz/Dib.php',
            'foo_bar\\baz_dib\\Zim_Gir' => 'foo_bar/baz_dib/Zim/Gir.php',
        );
        
        foreach ($list as $class => $expect) {
            $actual = $autoloader->classToFile($class);
            $expect = str_replace('/', DIRECTORY_SEPARATOR, $expect);
            $this->assertSame($expect, $actual);
        }
    }
}
