<?php

/**
 * Resolve the documented services in a real host-owned Laminas ServiceManager.
 *
 * The host installs laminas/laminas-servicemanager; it is not a domain/runtime package dependency.
 *
 * @since 0.1.0
 */

declare(strict_types=1);

use Kumwe\Localization\Application\ActiveLocale;
use Kumwe\Localization\Application\CatalogueTranslator;
use Kumwe\Localization\Application\DefaultLocaleProvider;
use Kumwe\Localization\Application\LocaleNegotiator;
use Kumwe\Localization\Application\MessageCatalogueRepository;
use Kumwe\Localization\Application\MessageOverrideRepository;
use Kumwe\Localization\Application\MessagePatternFormatter;
use Kumwe\Localization\Application\SupportedLocales;
use Kumwe\Localization\Application\Translator;
use Kumwe\Localization\ConfigProvider;
use Laminas\ServiceManager\ServiceManager;

$example = require __DIR__ . '/translate.php';
if (
    !is_array($example)
    || !($example['supported'] ?? null) instanceof SupportedLocales
    || !($example['active'] ?? null) instanceof ActiveLocale
    || !($example['default'] ?? null) instanceof DefaultLocaleProvider
    || !($example['catalogues'] ?? null) instanceof MessageCatalogueRepository
    || !($example['overrides'] ?? null) instanceof MessageOverrideRepository
    || !($example['formatter'] ?? null) instanceof MessagePatternFormatter
) {
    throw new RuntimeException('The explicit example adapters are incomplete.');
}

$provider = (new ConfigProvider())();
$dependencies = $provider['dependencies'];
$dependencies['services'] = [
    SupportedLocales::class => $example['supported'],
    ActiveLocale::class => $example['active'],
    DefaultLocaleProvider::class => $example['default'],
    MessageCatalogueRepository::class => $example['catalogues'],
    MessageOverrideRepository::class => $example['overrides'],
    MessagePatternFormatter::class => $example['formatter'],
    'config' => $provider,
];
$container = new ServiceManager($dependencies);
$first = $container->get(Translator::class);
$second = $container->get(Translator::class);
if (!$first instanceof CatalogueTranslator || !$second instanceof CatalogueTranslator || $first === $second) {
    throw new RuntimeException('Translator interface binding and non-shared lifetime did not resolve.');
}
$negotiated = $container->get(LocaleNegotiator::class);
if (!$negotiated instanceof LocaleNegotiator || $negotiated->negotiate(null)->toString() !== 'en-GB') {
    throw new RuntimeException('The configured locale negotiator did not resolve.');
}
if ($first->translate('core.example.greeting', ['name' => 'Kumwe']) !== 'Hello, Kumwe!') {
    throw new RuntimeException('The configured translator failed the installed catalogue example.');
}
echo "ServiceManager factories, alias and operation lifetimes verified.\n";
