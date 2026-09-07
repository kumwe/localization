<?php

/**
 * Adapt PHPUnit's actual discovery to the package ownership evidence format.
 * @since 0.1.1
 */

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

chdir(dirname(__DIR__));
$lines = [];
$status = 0;
exec(escapeshellarg(PHP_BINARY) . ' vendor/bin/phpunit --list-tests', $lines, $status);
if ($status !== 0) {
    throw new RuntimeException('PHPUnit discovery failed.');
}
$inventory = [];
foreach ($lines as $line) {
    if (preg_match('/^ - ([A-Za-z0-9_\\\\]+)::(test[A-Za-z0-9_]+)(?:#.*)?$/D', $line, $match) !== 1) {
        continue;
    }
    $class = $match[1];
    if (!class_exists($class)) {
        throw new RuntimeException('Discovered test class cannot be autoloaded.');
    }
    $method = new ReflectionMethod($class, $match[2]);
    $file = $method->getFileName();
    if ($file === false || !str_starts_with($file, dirname(__DIR__) . '/tests/')) {
        throw new RuntimeException('Discovered test is not package-owned.');
    }
    $inventory[$class . '::' . $match[2]] = substr($file, strlen(dirname(__DIR__)) + 1);
}
if ($inventory === []) {
    throw new RuntimeException('PHPUnit discovered no package tests.');
}
echo json_encode($inventory, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n";
