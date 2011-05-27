<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\framework\cli\hello_world;
use aura\cli\Command as CliCommand;

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
