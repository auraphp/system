<?php
$xml   = [];
$xml[] = '<phpunit bootstrap="./bootstrap.php" backupGlobals="false">';
$xml[] = '    <testsuites>';
$xml[] = '        <testsuite>';

$system = '..' . DIRECTORY_SEPARATOR
        . '..';

$file = $system . DIRECTORY_SEPARATOR
      . 'config' . DIRECTORY_SEPARATOR
      . '_packages';

$packages = file($file);

foreach ($packages as $package) {
    $package = trim($package);
    $file = $system . DIRECTORY_SEPARATOR
          . 'package' . DIRECTORY_SEPARATOR
          . $package . DIRECTORY_SEPARATOR
          . 'tests' . DIRECTORY_SEPARATOR
          . 'WiringTest.php';
    if (is_readable($file)) {
        $xml[] = "            <file>$file</file>";
    }
}

$xml[] = '        </testsuite>';
$xml[] = '    </testsuites>';
$xml[] = '</phpunit>';

file_put_contents('phpunit.xml', implode($xml, PHP_EOL));
