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
    
    protected function newCommand($argv = [], $system_dir = AURA_TEST_RUN_SYSTEM_DIR)
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
        $command = $this->newCommand(['package/Aura.Framework/src/NoSuchClass.php']);
        $command->exec();
    }
    
    /**
     * @expectedException Aura\Framework\Exception\TestFileExists
     */
    public function testTargetFileExists()
    {
        $command = $this->newCommand(['package/Aura.Framework/src/Aura/Framework/Cli/MakeTest/Command.php']);
        $command->exec();
    }
    
    /**
     * @todo check the resulting file contents, not just that it exists
     */
    public function test()
    {
        // write out a fake class in a fake package
        $vendor  = 'MockVendor';
        $package = 'MockPackage';
        $class   = 'MockClass';
        
        $system_dir = AURA_TEST_RUN_SYSTEM_DIR . DIRECTORY_SEPARATOR
                    . 'tmp' . DIRECTORY_SEPARATOR
                    . 'test' . DIRECTORY_SEPARATOR
                    . 'Aura.Framework.Cli.MakeTest.CommandTest' . DIRECTORY_SEPARATOR
                    . 'mock_system';
        
        $package_dir  = "$system_dir/package";
        
        $incl_file = "{$package_dir}/{$vendor}.{$package}/src/{$vendor}/{$package}/{$class}.php";
        $test_file = "{$package_dir}/{$vendor}.{$package}/tests/{$vendor}/{$package}/{$class}Test.php";
        
        @unlink($incl_file);
        @unlink($test_file);
        
        @mkdir(dirname($incl_file), 0777, true);
        @mkdir(dirname($test_file), 0777, true);
        
        $code = "<?php
namespace {$vendor}\\{$package};
class {$class} {}
";
        // write directly to the include dir instead of to a src dir and then
        // symlinking, to simplify things for the test
        file_put_contents($incl_file, $code);
        
        // make a test from the fake class
        $command = $this->newCommand(
            ["$package_dir/{$vendor}.{$package}/src/{$vendor}/{$package}/{$class}.php"],
            $system_dir
        );
        
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
