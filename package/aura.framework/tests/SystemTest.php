<?php
namespace aura\framework;

/**
 * Test class for System.
 */
class SystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var System
     */
    protected $system;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->system = new System(__DIR__);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @todo Implement testGetRootPath().
     */
    public function testGetRootPath()
    {
        $expect = __DIR__;
        $actual = $this->system->getRootPath();
        $this->assertSame($expect, $actual);
        
        $expect = __DIR__ . DIRECTORY_SEPARATOR
                . 'foo' . DIRECTORY_SEPARATOR
                . 'bar' . DIRECTORY_SEPARATOR
                . 'baz';
                
        $actual = $this->system->getRootPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Implement testGetPackagePath().
     */
    public function testGetPackagePath()
    {
        $expect = __DIR__ . DIRECTORY_SEPARATOR . 'package';
        $actual = $this->system->getPackagePath();
        $this->assertSame($expect, $actual);
        
        $expect = __DIR__ . DIRECTORY_SEPARATOR
                . 'package' . DIRECTORY_SEPARATOR
                . 'foo' . DIRECTORY_SEPARATOR
                . 'bar' . DIRECTORY_SEPARATOR
                . 'baz';
                
        $actual = $this->system->getPackagePath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }

    /**
     * @todo Implement testGetTmpPath().
     */
    public function testGetTmpPath()
    {
        $expect = __DIR__ . DIRECTORY_SEPARATOR . 'tmp';
        $actual = $this->system->getTmpPath();
        $this->assertSame($expect, $actual);
        
        $expect = __DIR__ . DIRECTORY_SEPARATOR
                . 'tmp' . DIRECTORY_SEPARATOR
                . 'foo' . DIRECTORY_SEPARATOR
                . 'bar' . DIRECTORY_SEPARATOR
                . 'baz';
                
        $actual = $this->system->getTmpPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }

    /**
     * @todo Implement testGetConfigPath().
     */
    public function testGetConfigPath()
    {
        $expect = __DIR__ . DIRECTORY_SEPARATOR . 'config';
        $actual = $this->system->getConfigPath();
        $this->assertSame($expect, $actual);
        
        $expect = __DIR__ . DIRECTORY_SEPARATOR
                . 'config' . DIRECTORY_SEPARATOR
                . 'foo' . DIRECTORY_SEPARATOR
                . 'bar' . DIRECTORY_SEPARATOR
                . 'baz';
                
        $actual = $this->system->getConfigPath('foo/bar/baz');
        $this->assertSame($expect, $actual);
    }

    // /**
    //  * @todo Implement testGetPublicPath().
    //  */
    // public function testGetPublicPath()
    // {
    //     $expect = __DIR__ . DIRECTORY_SEPARATOR . 'public';
    //     $actual = $this->system->getPublicPath();
    //     $this->assertSame($expect, $actual);
    //     
    //     $expect = __DIR__ . DIRECTORY_SEPARATOR
    //             . 'public' . DIRECTORY_SEPARATOR
    //             . 'foo' . DIRECTORY_SEPARATOR
    //             . 'bar' . DIRECTORY_SEPARATOR
    //             . 'baz';
    //             
    //     $actual = $this->system->getPublicPath('foo/bar/baz');
    //     $this->assertSame($expect, $actual);
    // }
}
