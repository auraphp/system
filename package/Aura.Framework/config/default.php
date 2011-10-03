<?php
/**
 * Package prefix for autoloader.
 */
$loader->addPrefix('Aura\Framework\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Instance params and setter values.
 */
$map =& $di->params['Aura\Cli\CommandFactory']['map'];
$map['aura.framework.make-test']    = 'Aura\Framework\Cli\MakeTest\Command';
$map['aura.framework.run-tests']    = 'Aura\Framework\Cli\RunTests\Command';
$map['aura.framework.hello-world']  = 'Aura\Framework\Cli\HelloWorld\Command';
$map['aura.framework.cache-config'] = 'Aura\Framework\Cli\CacheConfig\Command';
$map['aura.framework.cache-classmap'] = 'Aura\Framework\Cli\CacheClassmap\Command';

$di->params['Aura\Cli\CommandFactory']['not_found'] = 'Aura\Framework\Cli\NotFound\Command';

$di->setter['Aura\Framework\Cli\MakeTest\Command'] = array(
    'setInflect' => $di->lazyGet('inflect'),
    'setSystem'  => $di->lazyGet('system'),
);

$di->setter['Aura\Framework\Cli\RunTests\Command'] = array(
    'setPhpunit' => str_replace('/', DIRECTORY_SEPARATOR, "php " . dirname(__DIR__) . "/PHPUnit-3.4.15/phpunit.php"),
    'setSystem'  => $di->lazyGet('system'),
);

$di->setter['Aura\Framework\Cli\CacheConfig\Command'] = array(
    'setSystem'  => $di->lazyGet('system'),
);

$di->setter['Aura\Framework\Cli\CacheClassmap\Command'] = array(
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
 * Overrides for other packages.
 */
$di->setter['Aura\Web\Page']['setRouter'] = $di->lazyGet('router_map');

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
