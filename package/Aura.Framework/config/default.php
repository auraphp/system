<?php
/**
 * Package prefix for autoloader.
 */

$loader->addPrefix('Aura\Framework\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Instance params and setter values.
 */
 
$di->setter['Aura\Framework\Cli\Command']['setSignal'] = $di->lazyGet('signal_manager');

$di->setter['Aura\Framework\Cli\CacheClassmap\Command'] = array(
    'setSystem'  => $di->lazyGet('system'),
);

$di->setter['Aura\Framework\Cli\CacheConfig\Command'] = array(
    'setSystem'  => $di->lazyGet('system'),
);

$di->setter['Aura\Framework\Cli\MakeTest\Command'] = array(
    'setInflect' => $di->lazyGet('inflect'),
    'setSystem'  => $di->lazyGet('system'),
);

$phpunit = 'php -d include_path=' . dirname(__DIR__) . '/pear/php '
         . dirname(__DIR__) . '/pear/bin/phpunit --verbose';

$di->setter['Aura\Framework\Cli\RunTests\Command'] = array(
    'setPhpunit' => str_replace('/', DIRECTORY_SEPARATOR, $phpunit),
    'setSystem'  => $di->lazyGet('system'),
);

$di->params['Aura\Framework\Web\Factory'] = array(
    'forge' => $di->getForge(),
);

$di->params['Aura\Framework\Web\Front'] = array(
    'signal'    => $di->lazyGet('signal_manager'),
    'context'   => $di->lazyGet('web_context'),
    'router'    => $di->lazyGet('router_map'),
    'factory'   => $di->lazyNew('Aura\Framework\Web\Factory'),
    'response'  => $di->lazyNew('Aura\Http\Response'),
);

$di->setter['Aura\Framework\Web\Page'] = array(
    'setInflect' => $di->lazyGet('inflect'),
    'setRouter'  => $di->lazyGet('router_map'),
    'setSignal'  => $di->lazyGet('signal_manager'),
    'setSystem'  => $di->lazyGet('system'),
    'setView'    => $di->lazyNew('Aura\View\TwoStep'),
);

$di->setter['Aura\Framework\Web\Asset\Page'] = array(
    'setSystem' => $di->lazyGet('system'),
    'setWebCacheDir' => 'cache/asset',
    'setCacheConfigModes' => array('prod', 'staging'),
);

$di->params['Aura\View\HelperLocator']['registry']['assetHref'] = function() use ($di) {
    return $di->newInstance('Aura\Framework\View\Helper\AssetHref');
};

/**
 * Dependency services.
 */
$di->set('inflect', function() {
    return new Aura\Framework\Inflect;
});

$di->set('system', function() use ($system) {
    return new Aura\Framework\System($system);
});

$di->set('web_front', function() use ($di) {
    return $di->newInstance('Aura\Framework\Web\Front');
});
