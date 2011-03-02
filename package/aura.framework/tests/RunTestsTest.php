<?php
namespace aura\framework;
use aura\cli\Getopt as Getopt;
use aura\cli\Stdio as Stdio;
use aura\cli\Vt100 as Vt100;
use aura\cli\Context as Context;
use aura\cli\OptionFactory as OptionFactory;
use aura\signal\Manager;
use aura\signal\HandlerFactory;
use aura\signal\ResultFactory;
use aura\signal\ResultCollection;

/**
 * Test class for Run.
 */
class RunTestsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Run
     */
    protected $run;
    
    protected $stdio;
    
    protected $getopt;
    
    protected $system;
    
    protected $tmp_dir;
    
    protected $context;
    
    protected $signal;
    
    protected $phpunit;
    
    protected function newRun($argv = array(), $system_dir = AURA_TEST_RUN_SYSTEM_DIR)
    {
        $_SERVER['argv'] = $argv;
        $this->context = new Context;
        $this->system = new System($system_dir);
        $this->tmp_dir =  $this->system->getTmpPath('test/aura.test/Run');
        
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
        
        $this->phpunit = dirname(__DIR__) . DIRECTORY_SEPARATOR
                 . 'PHPUnit-3.4.15' . DIRECTORY_SEPARATOR
                 . 'phpunit.php';
        
        $run = new RunTests(
            $this->context,
            $this->stdio,
            $this->getopt,
            $this->signal
        );
        
        $run->setSystem($this->system);
        $run->setPhpunit($this->phpunit);
        
        return $run;
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
        $run = $this->newRun(array('foo/bar/BazTest.php'));
        $run->exec();
    }
    
    public function test()
    {
        $run = $this->newRun(array('package/aura.framework/tests/MakeTestTest.php', '--tap'));
        $run->exec();
        // there should have been no errors
        $err = file_get_contents($this->errfile);
        $this->assertSame('', $err);
    }
}
