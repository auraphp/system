<?php
/**
 * Instance params and setter values.
 */
$map =& $di->params['aura\cli\CommandFactory']['map'];
$map['aura.framework.make-test'] = 'aura\framework\MakeTest';
$map['aura.framework.run-tests'] = 'aura\framework\RunTests';

$di->setter['aura\framework\MakeTest'] = array(
    'setInflect' => $di->lazyGet('inflect'),
    'setSystem'  => $di->lazyGet('system'),
);

$di->setter['aura\framework\RunTests'] = array(
    'setPhpunit' => str_replace('/', DIRECTORY_SEPARATOR, "php " . dirname(__DIR__) . "/PHPUnit-3.4.15/phpunit.php"),
    'setSystem'  => $di->lazyGet('system'),
);

$di->params['aura\framework\Dispatcher'] = array(
    'context'            => $di->lazyGet('web_context'),
    'router'             => $di->lazyGet('router_map'),
    'controller_factory' => $di->lazyNew('aura\web\ControllerFactory'),
    'view'               => $di->lazyNew('aura\view\TwoStep'),
    'http_response'      => $di->lazyNew('aura\http\Response'),
    'signal'             => $di->lazyGet('signal_manager'),
    'loader'             => $loader,
);

/**
 * Dependency services.
 */
$di->set('inflect', function() {
    return new aura\framework\Inflect;
});

$di->set('system', function() use ($system) {
    return new aura\framework\System($system);
});

$di->set('dispatcher', function() use ($di) {
    return $di->newInstance('aura\framework\Dispatcher');
});
