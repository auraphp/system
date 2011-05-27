<?php
namespace aura\framework\cli\run_tests;
use aura\cli\Getopt as Getopt;
use aura\cli\Stdio as Stdio;
use aura\cli\Vt100 as Vt100;
use aura\cli\Context as Context;
use aura\cli\OptionFactory as OptionFactory;
use aura\signal\Manager;
use aura\signal\HandlerFactory;
use aura\signal\ResultFactory;
use aura\signal\ResultCollection;
use aura\framework\System;

/**
 * Test class for run_tests\Command.
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Run
     */
    protected $command;
    
    protected $stdio;
    
    protected $getopt;
    
    protected $system;
    
    protected $tmp_dir;
    
    protected $context;
    
    protected $signal;
    
    protected $phpunit;
    
    protected function newCommand($argv = array(), $system_dir = AURA_TEST_RUN_SYSTEM_DIR)
    {
        $_SERVER['argv'] = $argv;
        $this->context = new Context;
        $this->system = new System($system_dir);
        $this->tmp_dir =  $this->system->getTmpPath('test/aura.framework/cli/run_tests/Command');
        
        // use files because we can't use php://memory in proc_open() calls
        $this->outfile = tempnam($this->tmp_dir, '');
        $this->errfile = tempnam($this->tmp_dir, '');
        
        $stdin = fopen('php://stdin', 'r');
        $stdout = fopen($this->outfile, 'w+');
        $stderr = fopen($this->errfile, 'w+');
        $vt100 = new Vt100;
        $this->stdio = new Stdio($stdin, $stdout, $stderr, $vt100);
        
        $option_factory = new OptionFactory();
        $this->getopt = new Getopt($option_factory);
        
        $this->signal = new Manager(new HandlerFactory, new ResultFactory, new ResultCollection);
        
        $this->phpunit = $this->system->getPackagePath('aura.framework/PHPUnit-3.4.15/phpunit.php');
        
        $command = new Command(
            $this->context,
            $this->stdio,
            $this->getopt,
            $this->signal
        );
        
        $command->setSystem($this->system);
        $command->setPhpunit($this->phpunit);
        
        return $command;
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        fclose($this->stdio->getStdout());
        unlink($this->outfile);
        fclose($this->stdio->getStderr());
        unlink($this->errfile);
    }
    
    /**
     * @expectedException aura\framework\Exception_TestFileNotFound
     */
    public function test_noSuchFile()
    {
        $command = $this->newCommand(array('foo/bar/BazTest.php'));
        $command->exec();
    }
    
    public function testRunOne()
    {
        $command = $this->newCommand(array('package/aura.framework/tests/cli/make_test/CommandTest.php', '--tap'));
        $command->exec();
        // there should have been no errors
        $err = file_get_contents($this->errfile);
        $this->assertSame('', $err);
    }
}
