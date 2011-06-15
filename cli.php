<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;

// bootstrap
require __DIR__ . '/package/Aura.Framework/scripts/bootstrap.php';

// get the context and remove the invoking script name from the arguments
$context = $di->get('cli_context');

$context->shiftArgv();

// get the command factory
$command_factory = $di->get('cli_command_factory');

// instantiate a command object and execute
try {
    $command = $command_factory->newInstance($context->shiftArgv());
    $command->exec();
} catch (Exception $e) {
    echo $e . PHP_EOL;
    exit(1);
}
