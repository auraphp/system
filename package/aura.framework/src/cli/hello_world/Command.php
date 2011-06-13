<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli\hello_world;
use Aura\Cli\Command as CliCommand;

/**
 * 
 * A simple CLI command to output "Hello, World!"
 * 
 * @package aura.framework
 * 
 */
class Command extends CliCommand
{
    public function action()
    {
        $this->stdio->outln("Hello World!");
    }
}
