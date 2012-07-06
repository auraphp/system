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
$di->get('router_map')->add('home', '/', [
    'values' => [
        'controller' => 'hello',
        'action' => 'world',
    ],
]);

$di->get('router_map')->add('hello_world', '/hello/world', [
    'values' => [
        'controller' => 'hello',
        'action' => 'world',
    ],
]);

$di->get('router_map')->add('hello_asset', '/hello/asset', [
    'values' => [
        'controller' => 'hello',
        'action' => 'asset',
    ],
]);

$di->get('router_map')->add(null, '/asset/{:package}/{:file:(.*?)}{:format:(\..+)?}', [
    'values' => [
        'controller' => 'asset',
        'action' => 'index',
    ],
]);

// map the 'hello_world' controller value a particular class
$di->params['Aura\Framework\Web\Factory']['map']['hello'] = 'Aura\Framework\Web\Hello\Page';
$di->params['Aura\Framework\Web\Factory']['map']['asset'] = 'Aura\Framework\Web\Asset\Page';
$di->params['Aura\Framework\Web\Factory']['not_found'] = 'Aura\Framework\Web\NotFound\Page';

$di->set('signal_manager', function () use ($di) {
    return $di->newInstance('Aura\Framework\Signal\Manager');
});
