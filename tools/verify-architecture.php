<?php

/**
 * Verify the extracted dependency direction and prohibit host or compatibility ownership.
 *
 * @since 0.1.0
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$namespace = 'Kumwe\\Localization';
$layers = [
    'Domain' => ['Domain'],
    'Application' => ['Domain', 'Application'],
    'Infrastructure' => ['Domain', 'Application', 'Infrastructure'],
    'Container' => ['Domain', 'Application', 'Infrastructure', 'Container'],
    'ConfigProvider.php' => ['Application', 'Container'],
];
$failures = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $relative = substr($file->getPathname(), strlen($root . '/src/'));
    $layer = explode('/', $relative)[0];
    $code = file_get_contents($file->getPathname());
    if (!is_string($code)) {
        throw new RuntimeException('Cannot inspect ' . $relative);
    }
    $expected = $namespace . (dirname($relative) === '.' ? '' : '\\' . str_replace('/', '\\', dirname($relative)));
    if (!str_contains($code, 'namespace ' . $expected . ';') || !str_contains($code, 'declare(strict_types=1);')) {
        $failures[] = $relative . ' has incorrect namespace or strict types.';
    }
    foreach (token_get_all($code) as $token) {
        if (!is_array($token)) {
            continue;
        }
        if (
            $token[0] === T_STRING
            && in_array($token[1], ['class_alias', 'class_exists', 'setlocale', 'getenv'], true)
        ) {
            $failures[] = $relative . ' invokes prohibited process-state or compatibility behavior.';
        }
        if (!in_array($token[0], [T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
            continue;
        }
        $name = ltrim($token[1], '\\');
        if ($name === $namespace) {
            continue;
        }
        if (str_starts_with($name, $namespace . '\\')) {
            $target = explode('\\', substr($name, strlen($namespace) + 1))[0];
            if (!in_array($target, $layers[$layer] ?? [], true)) {
                $failures[] = $relative . ' crosses to forbidden layer ' . $target;
            }
        } elseif (
            str_contains($name, '\\')
            && !($layer === 'Container' && $name === 'Psr\\Container\\ContainerInterface')
        ) {
            $failures[] = $relative . ' names external type ' . $name;
        }
    }
}
$composer = json_decode((string) file_get_contents($root . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
if (
    !is_array($composer) || ($composer['require'] ?? null) !== [
    'php' => '^8.5', 'ext-intl' => '*', 'psr/container' => '^2.0',
    ]
) {
    $failures[] = 'Runtime dependencies differ from the reviewed PHP/ICU/PSR-11 contract.';
}
if ($failures !== []) {
    fwrite(STDERR, implode("\n", $failures) . "\n");
    exit(1);
}
echo "Localization ownership and dependency direction verified.\n";
