<?php
/**
 * Package prefix for autoloader.
 */
$loader->addPrefix('Aura\Framework\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Instance params and setter values.
 */
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

$di->setter['Aura\Framework\Cli\MakePackage\Command'] = array(
    'setSystem'  => $di->lazyGet('system'),
);

$di->params['Aura\Framework\RequestHandler'] = array(
    'context'    => $di->lazyGet('web_context'),
    'signal'     => $di->lazyGet('signal_manager'),
    'dispatcher' => $di->lazyNew('Aura\Framework\Dispatcher'),
    'renderer'   => $di->lazyNew('Aura\Framework\Renderer'),
    'responder'  => $di->lazyNew('Aura\Framework\Responder'),
);

$di->params['Aura\Framework\Dispatcher'] = array(
    'router'             => $di->lazyGet('router_map'),
    'controller_factory' => $di->lazyNew('Aura\Web\ControllerFactory'),
);

$di->params['Aura\Framework\Renderer'] = array(
    'view'   => $di->lazyNew('Aura\View\TwoStep'),
    'loader' => $loader,
);

$di->params['Aura\Framework\Responder'] = array(
    'response' => $di->lazyNew('Aura\Http\Response'),
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

$di->set('request_handler', function() use ($di) {
    return $di->newInstance('Aura\Framework\RequestHandler');
});

//Get or create the view_helper Container
$vhc = $di->subContainer('view_helper');

//params for Route which is a Aura\Router\Map object
$vhc->params['Aura\Framework\View\Helper\Router']['router'] = $di->lazyGet('router_map');

$vhc->set('router', function() use ($vhc) {
    return $vhc->newInstance('Aura\Framework\View\Helper\Router');
});
