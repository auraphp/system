<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;
require dirname(__DIR__) . '/package/Aura.Framework/scripts/bootstrap.php';
try {
    $request_handler = $di->get('request_handler');
    $response = $request_handler->exec();
    $response->send();
} catch (Exception $e) {
    echo $e . PHP_EOL;
    exit(1);
}
