<?php
/**
 * 
 * Overrides for 'default' config mode.
 * 
 * @var string $system Path to the Aura system root.
 * 
 * @var aura\framework\Autoloader $loader The autoloader for the system.
 * 
 * @var aura\di\Container $di The DI container for the system.
 * 
 */

// set up a simple "hello world" routing
$di->get('router_map')->add('home', '/', array(
    'values' => array(
        'controller' => 'hello_world',
        'action' => 'index',
    ),
));

// map the 'hello_world' controller value a particular class
$di->params['aura\web\ControllerFactory']['map']['hello_world'] = 'aura\framework\web\hello_world\Page';
$di->params['aura\web\ControllerFactory']['not_found'] = 'aura\framework\web\not_found\Page';
