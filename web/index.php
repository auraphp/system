<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;
require dirname(__DIR__) . '/package/Aura.Framework/src/Aura/Framework/Bootstrap.php';
$bootstrap = new Bootstrap();
$bootstrap->execWeb();