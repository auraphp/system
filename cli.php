<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\framework;

// bootstrap
require __DIR__ . '/package/aura.framework/scripts/bootstrap.php';

// get the context and remove the invoking script name from the arguments
$context = $di->get('cli_context');
$context->shiftArgv();

// factory a controller from the next argument
$controller_factory = $di->get('cli_controller_factory');

// get the cli controller and execute
try {
    $controller = $controller_factory->newInstance($context->shiftArgv());
    $controller->exec();
} catch (Exception $e) {
    echo $e . PHP_EOL;
    exit(1);
}
