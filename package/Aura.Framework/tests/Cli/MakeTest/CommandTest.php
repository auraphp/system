<?php
namespace Aura\Framework\Cli\MakeTest;
use Aura\Framework\Cli\AbstractCommandTest;
use Aura\Framework\Inflect;

/**
 * Test class for make_test\Command.
 */
class CommandTest extends AbstractCommandTest
{
    protected $command_name = 'MakeTest';
    
    protected $inflect;
    
    protected function newCommand($argv = array(), $system_dir = AURA_TEST_RUN_SYSTEM_DIR)
    {
        $command = parent::newCommand($argv, $system_dir);
        $this->inflect = new Inflect;
        $command->setSystem($this->system);
        $command->setInflect($this->inflect);
        return $command;
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
