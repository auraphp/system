<?php
namespace Aura\Framework\Cli\MakeTest;
use Aura\Cli\Getopt as Getopt;
use Aura\Cli\Stdio as Stdio;
use Aura\Cli\OptionFactory as OptionFactory;
use Aura\Cli\Vt100 as Vt100;
use Aura\Cli\Context as Context;
use Aura\Signal\Manager;
use Aura\Signal\HandlerFactory;
use Aura\Signal\ResultFactory;
use Aura\Signal\ResultCollection;
use Aura\Framework\System;
use Aura\Framework\Inflect;

/**
 * Test class for make_test\Command.
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Make
     */
    protected $command;
    
    protected $stdio;
    
    protected $getopt;
    
    protected $signal;
    
    protected $system;
    
    protected $inflect;
    
    protected $context;
    
    protected function newCommand($argv = array(), $system_dir = AURA_TEST_RUN_SYSTEM_DIR)
    {
        $_SERVER['argv'] = $argv;
        $this->context = new Context;
        
        $stdin = fopen('php://memory', 'r');
        $stdout = fopen('php://memory', 'w+');
        $stderr = fopen('php://memory', 'w+');
        $vt100 = new Vt100;
        
        $this->stdio = new Stdio($stdin, $stdout, $stderr, $vt100);
        
        $option_factory = new OptionFactory();
        $this->getopt = new Getopt($option_factory);
        
        $this->signal = new Manager(new HandlerFactory, new ResultFactory, new ResultCollection);
        
        $this->system = new System($system_dir);
        
        $this->inflect = new Inflect;
        
        $command = new Command(
            $this->context,
            $this->stdio,
            $this->getopt,
            $this->signal
        );
        
        $command->setSystem($this->system);
        $command->setInflect($this->inflect);
        
        return $command;
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    protected function _readOut()
    {
        $stdout = $this->stdio->getStdout();
        rewind($stdout);
        $out = '';
        while ($txt = fread($stdout, 8192) !== false) {
            $out .= $txt;
        }
        return $out;
    }
    
    protected function _readErr()
    {
        $stderr = $this->stdio->getStderr();
        rewind($stderr);
        $err = '';
        while ($txt = fread($stderr, 8192) !== false) {
            $err .= $txt;
        }
        return $err;
    }
    
    /**
     * @expectedException Aura\Framework\Exception\SourceNotFound
     */
    public function test_sourceNotFound()
    {
        $command = $this->newCommand(array('package/Aura.Framework/src/NoSuchClass.php'));
        $command->exec();
    }
    
    /**
     * @expectedException Aura\Framework\Exception\TestFileExists
     */
    public function testTargetFileExists()
    {
        $command = $this->newCommand(array('package/Aura.Framework/src/Cli/MakeTest/Command.php'));
        $command->exec();
    }
    
    /**
     * @todo check the resulting file contents, not just that it exists
     */
    public function test()
    {
        // write out a fake class in a fake package
        $vendor_name  = 'mock_vendor';
        $package_name = 'mock_package';
        $class_name   = 'MockClass';
        
        $system_dir = AURA_TEST_RUN_SYSTEM_DIR . DIRECTORY_SEPARATOR
                    . 'tmp' . DIRECTORY_SEPARATOR
                    . 'test' . DIRECTORY_SEPARATOR
                    . 'Aura.Framework.MakeTestTest' . DIRECTORY_SEPARATOR
                    . 'mock_system';
        
        $package_dir  = "$system_dir/package";
        
        $incl_file = "{$package_dir}/{$vendor_name}.{$package_name}/src/{$class_name}.php";
        $test_file = "{$package_dir}/{$vendor_name}.{$package_name}/tests/{$class_name}Test.php";
        
        @unlink($incl_file);
        @unlink($test_file);
        
        @mkdir(dirname($incl_file), 0777, true);
        @mkdir(dirname($test_file), 0777, true);
        
        $code = "<?php
namespace mock_vendor\\mock_package;
class MockClass {}
";
        // write directly to the include dir instead of to a src dir and then
        // symlinking, to simplify things for the test
        file_put_contents($incl_file, $code);
        
        // make a test from the fake class
        $command = $this->newCommand(array("$package_dir/mock_vendor.mock_package/src/MockClass.php"), $system_dir);
        
        // needs to be in the include-path
        include_once $incl_file;
        $command->exec();
        
        // find the output file
        $this->assertFileExists($test_file);
        
        // delete the fake class and the fake output file
        unlink($incl_file);
        unlink($test_file);
    }
}
