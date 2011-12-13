<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli;
class Runner
{
    public static function exec($class)
    {
        // bootstrap
        require dirname(dirname(dirname(dirname(__DIR__)))) . '/scripts/bootstrap.php';
        
        // get the context and remove the invoking script name from the arguments
        $context = $di->get('cli_context');
        $context->shiftArgv();
        
        // instantiate a command object and execute
        try {
            $command = $di->newInstance($class);
            $command->exec();
        } catch (Exception $e) {
            echo $e . PHP_EOL;
            exit(1);
        }
    }
}
