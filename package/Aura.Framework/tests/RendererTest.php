<?php
namespace Aura\Framework;
use Aura\Autoload\Loader;
use Aura\View\TwoStep as TwoStepView;
use Aura\Di\Config;
use Aura\Di\Forge;
use Aura\Di\Container;
use Aura\View\Template;
use Aura\View\Finder;
use Aura\Web\ResponseTransfer;

/**
 * Test class for Renderer.
 * Generated by PHPUnit on 2011-10-06 at 15:17:23.
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Renderer
     */
    protected $renderer;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $finder = new Finder();
        
        $helper_container = new Container(new Forge(new Config));
        
        $template = new Template($finder, $helper_container);
        
        $view = new TwoStepView($template);
        
        $loader = new Loader;
        $loader->addPrefix('Aura\Framework\Mock', __DIR__ . DIRECTORY_SEPARATOR . 'Mock');
        
        $this->renderer = new Renderer($view, $loader);
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
     * @todo Implement testExec().
     */
    public function testExec()
    {
        $transfer = new ResponseTransfer;
        
        $transfer->setView('index');
        $transfer->addViewStack('Aura\Framework\Mock', 'view');
        $transfer->setLayout('default');
        $transfer->addLayoutStack('Aura\Framework\Mock', 'layout');
        
        $this->renderer->exec($transfer);
        $actual = $transfer->getContent();
        $expect = "mock layout begins, mock view, mock layout ends";
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_contentExists()
    {
        $expect = 'content already in place, no need to render';
        $transfer = new ResponseTransfer;
        $transfer->setContent($expect);
        
        $this->renderer->exec($transfer);
        $actual = $transfer->getContent();
        $this->assertSame($expect, $actual);
    }
}
