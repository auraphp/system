<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package aura.framework
 * 
 */
namespace aura\framework;
use aura\autoload\Loader;

function load_config($file, $system, $loader, $di) {
    require_once $file;
}

class TestContainer
{
    public function __call($method, $params)
    {
        return $this;
    }
}

// begin with error reporting turned all the way up
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('html_errors', false);

// define the system directory
$system =  dirname(dirname(dirname(__DIR__)));
define('AURA_TEST_RUN_SYSTEM_DIR', $system);

// force the config mode
$config_mode = 'test';

/**
 * Autoloader
 */
require_once "$system/package/aura.autoload/src.php";
$loader = new Loader;
$loader->register();

/**
 * DI container
 */
$di = new TestContainer;

/**
 * Load config
 */
include_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

// kill off this variable, as it is likely to have closures; this avoids
// "Exception: Serialization of 'Closure' is not allowed". and done!
unset($di);
