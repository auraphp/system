<?php
/**
 * 
 * Overrides for 'default' config mode.
 * 
 * @var string $system Path to the Aura system root.
 * 
 * @var Aura\Framework\Autoloader $loader The autoloader for the system.
 * 
 * @var Aura\Di\Container $di The DI container for the system.
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
$di->params['Aura\Web\ControllerFactory']['map']['hello_world'] = 'Aura\Framework\Web\HelloWorld\Page';
$di->params['Aura\Web\ControllerFactory']['not_found'] = 'Aura\Framework\Web\NotFound\Page';
