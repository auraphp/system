<?php
/**
 * 
 * Disable backup of globals to avoid "Serialization of 'Closure' is not allowed"
 * errors.
 * 
 * @backupGlobals disabled
 * 
 */
class AuraPackagesTest extends PHPUnit_Framework_TestCase
{
    /**
     * 
     * The dependency injection container.
     * 
     * @var Aura\Di\Container
     * 
     */
    protected $di;
    
    /**
     * 
     * Setup.
     * 
     * @return void
     * 
     */
    protected function setUp()
    {
        $this->di = $GLOBALS['AURA_DI_CONTAINER'];
    }
    
    /**
     * 
     * Asserts that the autoloader has paths for a particular prefix.
     * 
     * @param string $prefix
     * 
     * @return void
     * 
     */
    protected function assertLoaderHasPathsFor($prefix)
    {
        $loader = $this->di->get('framework_loader');
        $paths = $loader->getPaths();
        $this->assertTrue(array_key_exists($prefix, $paths));
    }
    
    /**
     * 
     * Asserts that a DI service is an instance of a particular class; this
     * tests that the service is instantiated without wiring errors.
     * 
     * @param string $service The service name.
     * 
     * @param string $class The class name.
     * 
     * @return void
     * 
     */
    protected function assertServiceIsInstanceOf($service, $class)
    {
        $object = $this->di->get($service);
        $this->assertInstanceOf($class, $object);
        return $object;
    }
    
    /**
     * 
     * Asserts that a new instance is of a particular class; this tests that
     * the class is instantiated without wiring errors.
     * 
     * @param string $expect Should be an instance of this class.
     * 
     * @param string $actual An alternative class to instantiate; useful for
     * mocks of abstract expected classes.
     * 
     * @return void
     * 
     */
    protected function assertNewInstanceOf($expect, $actual = null)
    {
        if (! $actual) {
            $actual = $expect;
        }
        $object = $this->di->newInstance($actual);
        $this->assertInstanceOf($expect, $object);
        return $object;
    }
    
    public function testAutoloadPackage()
    {
        /**
         * Loader
         */
        $this->assertLoaderHasPathsFor('Aura\\Autoload\\');
    }
    
    public function testCliPackage()
    {
        /**
         * Loader
         */
        $this->assertLoaderHasPathsFor('Aura\\Cli\\');
        
        /**
         * Services
         */
        $this->assertServiceIsInstanceOf('cli_context', 'Aura\Cli\Context');
        $this->assertServiceIsInstanceOf('cli_stdio', 'Aura\Cli\Stdio');
        
        /**
         * Aura\Cli\AbstractCommand
         */
        $this->assertNewInstanceOf('Aura\Cli\AbstractCommand', 'AuraCliAbstractCommand');
        
        /**
         * Aura\Intl\PackageLocator
         */
        $factory   = $this->di->newInstance('Aura\Cli\ExceptionFactory');
        $exception = $factory->newInstance('ERR_OPTION_NOT_DEFINED');
        $expect    = "The option '{option}' is not recognized.";
        $actual    = $exception->getMessage();
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @todo Check for cli, web, and router integration?
     */
    public function testDemoPackage()
    {
        /**
         * Loader
         */
        $this->assertLoaderHasPathsFor('Aura\\Demo\\');
    }
    
    public function testDiPackage()
    {
        /**
         * Loader
         */
        $this->assertLoaderHasPathsFor('Aura\\Di\\');
    }
    
    public function testFilterPackage()
    {
        /**
         * Loader
         */
        $this->assertLoaderHasPathsFor('Aura\\Filter\\');
        
        /**
         * Aura\Intl\PackageLocator, along with rule collection and the 'any' rule
         */
        $filter = $this->di->newInstance('Aura\Filter\RuleCollection');
        $filter->addSoftRule('foo', $filter::IS, 'any', [['alnum'], ['email']]);
        $object = (object) ['foo' => '!@#$'];
        $this->assertFalse($filter->values($object));
        $actual = $filter->getMessages();
        $expect = [
            'foo' => [
                'This field did not pass any of the sub-rules.',
            ],
        ];
        $this->assertSame($expect, $actual);
    }
    
    public function testFrameworkPackage()
    {
        /**
         * Loader
         */
        $this->assertLoaderHasPathsFor('Aura\\Framework\\');
        
        /**
         * Services
         */
        $this->assertServiceIsInstanceOf('framework_inflect', 'Aura\Framework\Inflect');
        $this->assertServiceIsInstanceOf('web_front', 'Aura\Framework\Web\Controller\Front');
        $this->assertServiceIsInstanceOf('signal_manager', 'Aura\Framework\Signal\Manager');
        
        /**
         * Aura\Router\Map
         */
        $map = $this->di->get('router_map');
        $route = $map->match('/asset/Vendor.Package/foo/bar/baz.ext', []);
        $actual = $route->values;
        $expect = [
            'controller' => 'aura.framework.asset',
            'action' => 'index',
            'package' => 'Vendor.Package',
            'file' => 'foo/bar/baz',
            'format' => '.ext',
        ];
        $this->assertSame($expect, $actual);
        
        /**
         * Aura\Framework\Bootstrap\Cli
         */
        $this->assertNewInstanceOf('Aura\Framework\Bootstrap\Cli');
        
        /**
         * Aura\Framework\Bootstrap\Web
         */
        $this->assertNewInstanceOf('Aura\Framework\Bootstrap\Web');
        
        /**
         * Aura\Framework\Cli\AbstractCommand
         */
        $this->assertNewInstanceOf('Aura\Framework\Cli\AbstractCommand', 'AuraFrameworkCliAbstractCommand');
        
        /**
         * Aura\Framework\Cli\CacheClassmap\Command
         */
        $this->assertNewInstanceOf('Aura\Framework\Cli\CacheClassmap\Command');
        
        /**
         * Aura\Framework\Cli\CacheConfig\Command
         */
        $this->assertNewInstanceOf('Aura\Framework\Cli\CacheConfig\Command');

        /**
         * Aura\Framework\Cli\Factory
         */
        $this->assertNewInstanceOf('Aura\Framework\Cli\Factory');

        /**
         * Aura\Framework\Cli\Server\Command
         */
        $this->assertNewInstanceOf('Aura\Framework\Cli\Server\Command');

        /**
         * Aura\Framework\View\Helper\AssetHref
         */
        $this->assertNewInstanceOf('Aura\Framework\View\Helper\AssetHref');

        /**
         * Aura\Framework\View\Helper\Route
         */
        $this->assertNewInstanceOf('Aura\Framework\View\Helper\Route');

        /**
         * Aura\Framework\Web\Asset\Page
         */
        $this->assertNewInstanceOf('Aura\Framework\Web\Asset\Page');

        /**
         * Aura\Framework\Web\Controller\AbstractPage
         */
        $this->assertNewInstanceOf('Aura\Framework\Web\Controller\AbstractPage', 'AuraFrameworkWebControllerAbstractPage');

        /**
         * Aura\Framework\Web\Controller\Factory
         */
        $this->assertNewInstanceOf('Aura\Framework\Web\Controller\Factory');

        /**
         * Aura\Framework\Web\Controller\Front
         */
        $this->assertNewInstanceOf('Aura\Framework\Web\Controller\Front');

        /**
         * Aura\Framework\Web\Renderer\AuraViewTwoStep
         */
        $this->assertNewInstanceOf('Aura\Framework\Web\Renderer\AuraViewTwoStep');

        /**
         * Aura\Intl\TranslatorLocator
         */
        $this->assertNewInstanceOf('Aura\Intl\TranslatorLocator');

        /**
         * Aura\View\HelperLocator
         */
        $helper = $this->assertNewInstanceOf('Aura\View\HelperLocator');
        $this->assertInstanceOf('Aura\Framework\View\Helper\AssetHref', $helper->get('assetHref'));
        $this->assertInstanceOf('Aura\Framework\View\Helper\Route', $helper->get('route'));
    }
    
    public function testHttpPackage()
    {
        /**
         * Loader
         */
        $this->assertLoaderHasPathsFor('Aura\Http\\');

        /**
         * Services
         */
        $this->assertServiceIsInstanceOf('http_transport', 'Aura\Http\Transport');
        $this->assertServiceIsInstanceOf('http_manager', 'Aura\Http\Manager');

        /**
         * Aura\Http\Adapter\Curl
         */
        $this->assertNewInstanceOf('Aura\Http\Adapter\Curl');

        /**
         * Aura\Http\Adapter\Stream
         */
        $this->assertNewInstanceOf('Aura\Http\Adapter\Stream');

        /**
         * Aura\Http\Cookie\Collection
         */
        $this->assertNewInstanceOf('Aura\Http\Cookie\Collection');

        /**
         * Aura\Http\Header\Collection
         */
        $this->assertNewInstanceOf('Aura\Http\Header\Collection');

        /**
         * Aura\Http\Manager
         */
        $this->assertNewInstanceOf('Aura\Http\Manager');

        /**
         * Aura\Http\Message
         */
        $this->assertNewInstanceOf('Aura\Http\Message');

        /**
         * Aura\Http\Message\Response\StackBuilder
         */
        $this->assertNewInstanceOf('Aura\Http\Message\Response\StackBuilder');

        /**
         * Aura\Http\Multipart\FormData
         */
        $this->assertNewInstanceOf('Aura\Http\Multipart\FormData');

        /**
         * Aura\Http\Transport
         */
        $this->assertNewInstanceOf('Aura\Http\Transport');
    }
}
