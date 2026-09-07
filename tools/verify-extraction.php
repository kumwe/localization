<?php

/**
 * Verify moved executable tokens against the captured immutable App baseline without an App checkout.
 *
 * Formatting and documentation are ignored; only the canonical namespace and the reviewed default-locale
 * interface inversion are normalized. Any other executable drift needs a reviewed successor decision.
 *
 * @since 0.1.0
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$raw = file_get_contents($root . '/resources/extraction/v1.json');
if (!is_string($raw)) {
    throw new RuntimeException('The extraction baseline is missing.');
}
$manifest = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
if (
    !is_array($manifest)
    || ($manifest['schema'] ?? null) !== 'kumwe-localization-extraction/v1'
    || ($manifest['source_repository'] ?? null) !== 'https://github.com/kumwe/app'
    || ($manifest['baseline_commit'] ?? null) !== '960ce8ec00cf724a7cae03e5ba09c4852c9ab54e'
    || !is_array($manifest['symbols'] ?? null)
    || $manifest['symbols'] === []
) {
    throw new RuntimeException('The extraction baseline is malformed.');
}
$introduced = [
    'src/Application/DefaultLocaleProvider.php',
    'src/ConfigProvider.php',
    'src/Container/CatalogueTranslatorFactory.php',
    'src/Container/LocaleNegotiatorFactory.php',
];
$expected = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = substr($file->getPathname(), strlen($root) + 1);
    if (!in_array($path, $introduced, true)) {
        $expected[$path] = true;
    }
}
$seen = [];
$count = 0;
foreach ($manifest['symbols'] as $entry) {
    if (!is_array($entry) || !is_string($entry['target_path'] ?? null) || !is_string($entry['token_sha256'] ?? null)) {
        throw new RuntimeException('The extraction baseline has an invalid symbol entry.');
    }
    $path = $entry['target_path'];
    if (
        preg_match('/^src\/(Application|Domain|Infrastructure)\/[A-Za-z]+\.php$/D', $path) !== 1
        || !isset($expected[$path])
        || isset($seen[$path])
        || preg_match('/^[a-f0-9]{64}$/D', $entry['token_sha256']) !== 1
        || !is_string($entry['source_sha256'] ?? null)
        || preg_match('/^[a-f0-9]{64}$/D', $entry['source_sha256']) !== 1
    ) {
        throw new RuntimeException('The extraction inventory contains an invalid or duplicate source path/digest.');
    }
    $seen[$path] = true;
    $code = file_get_contents($root . '/' . $entry['target_path']);
    if (!is_string($code)) {
        throw new RuntimeException('Cannot inspect extracted source.');
    }
    $code = str_replace('Kumwe\\Localization', 'Kumwe\\App\\Localization', $code);
    if ($entry['target_path'] === 'src/Application/LocaleNegotiator.php') {
        $code = str_replace('DefaultLocaleProvider', 'SiteDefaultLocale', $code);
    }
    $tokens = [];
    foreach (token_get_all($code) as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            $tokens[] = [$token[0], $token[1]];
        } else {
            $tokens[] = $token;
        }
    }
    $digest = hash('sha256', json_encode($tokens, JSON_THROW_ON_ERROR));
    if ($digest !== $entry['token_sha256']) {
        throw new RuntimeException('Unreviewed executable extraction drift in ' . $entry['target_path']);
    }
    ++$count;
}
if (array_diff_key($expected, $seen) !== []) {
    throw new RuntimeException('The extraction inventory omits production sources.');
}
echo "Executable extraction parity verified for {$count} original types.\n";
