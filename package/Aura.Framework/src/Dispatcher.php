<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;
use Aura\Web\Context;
use Aura\Web\ControllerFactory;
use Aura\Router\Map as RouterMap;

/**
 * 
 * Dispatches a Route to a Controller and returns a ResponseTransfer object.
 * 
 * @package Aura.Framework
 * 
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * 
     * A factory to create web controllers.
     * 
     * @var Aura\Web\ControllerFactory
     * 
     */
    protected $controller_factory;
    
    /**
     * 
     * A router map to dipatch against.
     * 
     * @var Aura\Router\Map
     * 
     */
    protected $router;
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(
        RouterMap         $router,
        ControllerFactory $controller_factory
    ) {
        $this->router             = $router;
        $this->controller_factory = $controller_factory;
    }
    
    /**
     * 
     * Creates and executes a web controller.
     * 
     * @return Aura\Web\ResponseTransfer
     * 
     */
    public function exec($path, array $server = null)
    {
        // match to a route
        $route = $this->router->match($path, $server);
        
        // was there a match?
        if ($route) {
            // retain info
            $controller = $route->values['controller'];
            $params     = $route->values;
        } else {
            // no match
            $controller = null;
            $params     = array();
        }
        
        // create controller
        $obj = $this->controller_factory->newInstance($controller, $params);
        
        // execute and return data transfer object
        return $obj->exec();
    }
}
