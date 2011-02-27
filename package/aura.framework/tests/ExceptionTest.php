<?php
namespace aura\framework;
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    public function testGetMessage_generic()
    {
        $e = new Exception();
        $expect = "Generic aura\\framework exception";
        $actual = $e->getMessage();
        $this->assertEquals($expect, $actual);
    }
    
    public function testGetMessage_package()
    {
        $e = new MockException();
        $expect = "Package aura\\framework: MockException";
        $actual = $e->getMessage();
        $this->assertEquals($expect, $actual);
    }
    
    public function test__toString()
    {
        $e = new Exception();
        $actual = $e->__toString();
        $expect = "exception 'aura\\framework\\Exception'
with message 'Generic aura\\framework exception' 
information array (
) 
Stack trace:
  #0 [internal function]: aura\framework\ExceptionTest->test__toString()
  #1 /usr/lib/php/PHPUnit/Framework/TestCase.php(844): ReflectionMethod->invokeArgs(Object(aura\framework\ExceptionTest), Array)
  #2 /usr/lib/php/PHPUnit/Framework/TestCase.php(723): PHPUnit_Framework_TestCase->runTest()
  #3 /usr/lib/php/PHPUnit/Framework/TestResult.php(686): PHPUnit_Framework_TestCase->runBare()
  #4 /usr/lib/php/PHPUnit/Framework/TestCase.php(666): PHPUnit_Framework_TestResult->run(Object(aura\framework\ExceptionTest))
  #5 /usr/lib/php/PHPUnit/Framework/TestSuite.php(763): PHPUnit_Framework_TestCase->run(Object(PHPUnit_Framework_TestResult))
  #6 /usr/lib/php/PHPUnit/Framework/TestSuite.php(739): PHPUnit_Framework_TestSuite->runTest(Object(aura\framework\ExceptionTest), Object(PHPUnit_Framework_TestResult))
  #7 /usr/lib/php/PHPUnit/TextUI/TestRunner.php(349): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
  #8 /usr/lib/php/PHPUnit/TextUI/Command.php(213): PHPUnit_TextUI_TestRunner->doRun(Object(PHPUnit_Framework_TestSuite), Array)
  #9 /usr/lib/php/PHPUnit/TextUI/Command.php(146): PHPUnit_TextUI_Command->run(Array, true)
  #10 /usr/bin/phpunit(54): PHPUnit_TextUI_Command::main()
  #11 {main}

Generic aura\\framework exception";

        // get rid of backtrace lines
        $expect = preg_replace('/^  #.*\n/m', '', $expect);
        $actual = preg_replace('/^  #.*\n/m', '', $actual);
        
        // now compare
        $this->assertEquals($expect, $actual);
    }
    
    public function testGetInfo_all()
    {
        $expect = array('foo' => 'bar', 'baz' => 'dib');
        $e = new Exception($expect);
        $actual = $e->getInfo();
        $this->assertSame($expect, $actual);
    }
    
    public function testGetInfo_key()
    {
        $info = array('foo' => 'bar', 'baz' => 'dib');
        $e = new Exception($info);
        $expect = 'bar';
        $actual = $e->getInfo('foo');
        $this->assertSame($expect, $actual);
    }
}
