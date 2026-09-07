<?php

/**
 * Run a complete translation with explicit in-memory host adapters.
 *
 * Pass a consumer Composer autoloader as argv[1] when running from an installed dependency.
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
use Kumwe\Localization\Application\SupportedLocales;
use Kumwe\Localization\Application\TranslationScope;
use Kumwe\Localization\Domain\LocaleTag;
use Kumwe\Localization\Domain\MessageCatalogue;
use Kumwe\Localization\Domain\MessageCatalogueLayer;
use Kumwe\Localization\Infrastructure\IntlMessagePatternFormatter;

$autoload = $argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php';
require_once $autoload;

$supported = new SupportedLocales();
$active = new ActiveLocale($supported);
$formatter = new IntlMessagePatternFormatter();
$default = new class implements DefaultLocaleProvider {
    /**
     * Supply the host's explicit fallback locale.
     *
     * @return LocaleTag The English source locale.
     * @since 0.1.0
     */
    public function locale(): LocaleTag
    {
        return LocaleTag::fromString('en-GB');
    }
};
$catalogues = new class implements MessageCatalogueRepository {
    /**
     * Supply a small catalogue without file or application dependencies.
     *
     * @param MessageCatalogueLayer $layer Requested file-shipped layer.
     * @param LocaleTag $locale Exact locale requested by the translator.
     *
     * @return MessageCatalogue The example core catalogue, empty for other layers/locales.
     * @since 0.1.0
     */
    public function catalogue(MessageCatalogueLayer $layer, LocaleTag $locale): MessageCatalogue
    {
        $messages = $layer === MessageCatalogueLayer::Core && $locale->toString() === 'en-GB'
            ? ['core.example.greeting' => 'Hello, {name}!']
            : [];

        return new MessageCatalogue($locale, $layer, $messages);
    }
};
$overrides = new class implements MessageOverrideRepository {
    /**
     * This host example has no administered site wording.
     *
     * @param string $site Explicit trusted site identifier.
     * @param LocaleTag $locale Exact locale requested.
     *
     * @return array<string, string> Empty overrides.
     * @since 0.1.0
     */
    public function siteOverrides(string $site, LocaleTag $locale): array
    {
        return [];
    }

    /**
     * This host example has no administered organization wording.
     *
     * @param string $site Explicit trusted site identifier.
     * @param string $organization Explicit trusted organization identifier.
     * @param LocaleTag $locale Exact locale requested.
     *
     * @return array<string, string> Empty overrides.
     * @since 0.1.0
     */
    public function organizationOverrides(string $site, string $organization, LocaleTag $locale): array
    {
        return [];
    }
};
$negotiator = new LocaleNegotiator($supported, $default);
$translator = new CatalogueTranslator($catalogues, $overrides, $formatter, $active, $supported);
$active->begin($negotiator->negotiate('en-GB'), new TranslationScope('example'));
try {
    $message = $translator->translate('core.example.greeting', ['name' => 'Kumwe']);
    if ($message !== 'Hello, Kumwe!') {
        throw new RuntimeException('The installed translation example produced unexpected output.');
    }
    echo $message . "\n";
} finally {
    $active->end();
}

return compact('supported', 'active', 'default', 'catalogues', 'overrides', 'formatter');
