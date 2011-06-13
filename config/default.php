<?php
/**
 * Constructor params.
 */
$di->params['Aura\Http\Response'] = array(
    'headers' => $di->lazyNew('Aura\Http\Headers'),
    'cookies' => $di->lazyNew('Aura\Http\Cookies'),
);

/**
 * Dependency services.
 */
$di->set('http_response', function() use ($di) {
    return $di->newInstance('Aura\Http\Response');
});
