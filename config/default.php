<?php
/**
 * 
 * Overrides for 'default' config mode.
 * 
 * @var string $system Path to the Aura system root.
 * 
 * @var Aura\Autoload\Loader $loader The autoloader for the system.
 * 
 * @var Aura\Di\Container $di The DI container for the system.
 * 
 */

// set up a simple "hello world" routing
$di->get('router_map')->add('home', '/', array(
    'values' => array(
        'controller' => 'hello',
        'action' => 'world',
    ),
));

$di->get('router_map')->add('hello_world', '/hello/world', array(
    'values' => array(
        'controller' => 'hello',
        'action' => 'world',
    ),
));

$di->get('router_map')->add('hello_asset', '/hello/asset', array(
    'values' => array(
        'controller' => 'hello',
        'action' => 'asset',
    ),
));

$di->get('router_map')->add(null, '/asset/{:package}/{:file:(.*)}', array(
    'values' => array(
        'controller' => 'asset',
        'action' => 'index',
    ),
));

// map the 'hello_world' controller value a particular class
$di->params['Aura\Web\ControllerFactory']['map']['hello'] = 'Aura\Framework\Web\Hello\Page';
$di->params['Aura\Web\ControllerFactory']['map']['asset'] = 'Aura\Framework\Web\Asset\Page';
$di->params['Aura\Web\ControllerFactory']['not_found'] = 'Aura\Framework\Web\NotFound\Page';
