<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\framework;
require dirname(__DIR__) . '/package/aura.framework/scripts/bootstrap.php';
$dispatcher = $di->get('framework_dispatcher');

$context = $di->get('web_context');
try {
    $response = $dispatcher->exec();
    $response->send();
} catch (Exception $e) {
    echo $e . PHP_EOL;
    exit(1);
}
