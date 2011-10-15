<?php
namespace Aura\Framework\Cli\RunTests;
use Aura\Framework\Cli\AbstractCommandTest;
use Aura\Framework\Inflect;

/**
 * Test class for run_tests\Command.
 */
class CommandTest extends AbstractCommandTest
{
    protected $command_name = 'RunTests';
    
    protected $phpunit;
    
    protected function newCommand($argv = array(), $system_dir = AURA_TEST_RUN_SYSTEM_DIR)
    {
        $command = parent::newCommand($argv, $system_dir);
        $this->phpunit = $this->system->getPackagePath('Aura.Framework/PHPUnit-3.4.15/phpunit.php');
        $command->setSystem($this->system);
        $command->setPhpunit($this->phpunit);
        return $command;
    }
    
    /**
     * @expectedException Aura\Framework\Exception\TestFileNotFound
     */
    public function test_noSuchFile()
    {
        $command = $this->newCommand(array('foo/bar/BazTest.php'));
        $command->exec();
    }
    
    public function testRunOne()
    {
        $command = $this->newCommand(array('package/Aura.Framework/tests/Cli/MakeTest/CommandTest.php', '--tap'));
        $command->exec();
        // there should have been no errors
        $err = file_get_contents($this->errfile);
        $this->assertSame('', $err);
    }
    
    // // comment out to cut testing time in half
    // public function testRunAll()
    // {
    //     $command = $this->newCommand(array('--exclude-package=Aura.Framework'));
    //     $command->exec();
    //     // there should have been no errors
    //     $err = file_get_contents($this->errfile);
    //     $this->assertSame('', $err);
    // }
}
