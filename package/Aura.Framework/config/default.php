<?php
/**
 * Instance params and setter values.
 */
$map =& $di->params['Aura\Cli\CommandFactory']['map'];
$map['aura.framework.make-test']   = 'Aura\Framework\Cli\MakeTest\Command';
$map['aura.framework.run-tests']   = 'Aura\Framework\Cli\RunTests\Command';
$map['aura.framework.hello-world'] = 'Aura\Framework\Cli\HelloWorld\Command';

$di->setter['Aura\Framework\Cli\MakeTest\Command'] = array(
    'setInflect' => $di->lazyGet('inflect'),
    'setSystem'  => $di->lazyGet('system'),
);

$di->setter['Aura\Framework\Cli\RunTests\Command'] = array(
    'setPhpunit' => str_replace('/', DIRECTORY_SEPARATOR, "php " . dirname(__DIR__) . "/PHPUnit-3.4.15/phpunit.php"),
    'setSystem'  => $di->lazyGet('system'),
);

$di->params['Aura\Framework\Dispatcher'] = array(
    'context'            => $di->lazyGet('web_context'),
    'router'             => $di->lazyGet('router_map'),
    'controller_factory' => $di->lazyNew('Aura\Web\ControllerFactory'),
    'view'               => $di->lazyNew('Aura\View\TwoStep'),
    'http_response'      => $di->lazyNew('Aura\Http\Response'),
    'signal'             => $di->lazyGet('signal_manager'),
    'loader'             => $loader,
);

$di->setter['Aura\Framework\Web\Asset\Page'] = array(
    'setSystem' => $di->lazyGet('system'),
    'setWebCacheDir' => 'cache/asset',
    'setCacheConfigModes' => array('prod', 'staging'),
);

/**
 * Dependency services.
 */
$di->set('inflect', function() {
    return new Aura\Framework\Inflect;
});

$di->set('system', function() use ($system) {
    return new Aura\Framework\System($system);
});

$di->set('dispatcher', function() use ($di) {
    return $di->newInstance('Aura\Framework\Dispatcher');
});
