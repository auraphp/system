<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package Aura.Framework
 * 
 */
namespace Aura\Framework;
use Aura\Autoload\Loader;
use Aura\Di\Manager;
use Aura\Di\Forge;
use Aura\Di\Config;

/**
 * 
 * Loads config files in a restricted scope.
 * 
 * @return void
 * 
 */
function load_config($file, $system, $loader, $di) {
    require_once $file;
}

/**
 * Setup.
 */
// turn up error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('html_errors', false);

// the aura system directory
$system = dirname(dirname(dirname(__DIR__)));

// what config mode to operate in?
$config_mode = empty($_ENV['AURA_CONFIG_MODE'])
            ? 'default'
            : $_ENV['AURA_CONFIG_MODE'];

// force the include path
set_include_path("$system/include");

/**
 * Autoloader
 */
 
require "$system/package/Aura.Autoload/src.php";

$loader = new Loader;
$loader->register();

/**
 * DI container
 */
$loader->addPrefix('Aura\Di\\', "$system/package/Aura.Di/src");
$di = new Manager(new Forge(new Config));

/**
 * Config and autoload registration
 */
$cache_file = $system . DIRECTORY_SEPARATOR
            . 'tmp' . DIRECTORY_SEPARATOR
            . 'cache' . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . $config_mode . '.php';

if (file_exists($cache_file)) {
    load_config($cache_file, $system, $loader, $di);
    $di->lock();
    return;
}

/**
 * Load config from scratch
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
$di->lock();
