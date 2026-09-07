<?php

declare(strict_types=1);

namespace Kumwe\Localization\Tests\Unit;

use InvalidArgumentException;
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
use Kumwe\Localization\Container\CatalogueTranslatorFactory;
use Kumwe\Localization\Container\LocaleNegotiatorFactory;
use Kumwe\Localization\Domain\LocaleTag;
use Kumwe\Localization\Infrastructure\IntlMessagePatternFormatter;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Exercises explicit factories, interface binding, scope lifetime and configuration refusal.
 *
 * @since 0.1.0
 */
#[CoversClass(ConfigProvider::class)]
#[CoversClass(CatalogueTranslatorFactory::class)]
#[CoversClass(LocaleNegotiatorFactory::class)]
final class ContainerTest extends TestCase
{
    /**
     * Provider configuration is deterministic and resolves fresh services through a real ServiceManager.
     *
     * @return void
     * @since 0.1.0
     */
    public function testServicesAndLifetimes(): void
    {
        $provider = (new ConfigProvider())();
        self::assertSame($provider, (new ConfigProvider())());
        self::assertFalse($provider['dependencies']['shared'][CatalogueTranslator::class]);
        self::assertFalse($provider['dependencies']['shared'][LocaleNegotiator::class]);
        $container = $this->container();
        self::assertInstanceOf(CatalogueTranslator::class, $container->get(Translator::class));
        self::assertNotSame($container->get(Translator::class), $container->get(Translator::class));
        self::assertNotSame($container->get(LocaleNegotiator::class), $container->get(LocaleNegotiator::class));
        self::assertSame('de', $container->get(LocaleNegotiator::class)->negotiate(null)->toString());
    }

    /**
     * Package options reject malformed configuration before constructing a negotiator.
     *
     * @return void
     * @since 0.1.0
     */
    public function testInvalidConfigurationShapesFailClosed(): void
    {
        foreach (['bad', ['kumwe' => 'bad'], ['kumwe' => ['localization' => 'bad']]] as $config) {
            try {
                (new LocaleNegotiatorFactory())($this->container(['config' => $config]));
                self::fail('Malformed configuration was accepted.');
            } catch (InvalidArgumentException) {
                self::assertTrue(true);
            }
        }
        foreach ([0, -1, '512', false] as $maximum) {
            try {
                (new LocaleNegotiatorFactory())($this->container([
                    'config' => ['kumwe' => ['localization' => ['maximum_accept_language_bytes' => $maximum]]],
                ]));
                self::fail('Invalid header bound was accepted.');
            } catch (InvalidArgumentException) {
                self::assertTrue(true);
            }
        }
    }

    /**
     * An explicitly smaller limit is honored, and null configuration is not accepted as an array.
     *
     * @return void
     * @since 0.1.0
     */
    public function testPositiveLimitAndWrongBindings(): void
    {
        $negotiator = (new LocaleNegotiatorFactory())($this->container([
            'config' => ['kumwe' => ['localization' => ['maximum_accept_language_bytes' => 2]]],
        ]));
        self::assertSame('de', $negotiator->negotiate(null, 'en-GB')->toString());
        $this->expectException(InvalidArgumentException::class);
        (new CatalogueTranslatorFactory())($this->container([MessageCatalogueRepository::class => new \stdClass()]));
    }

    /**
     * A mismatched default provider never becomes an implicit fallback implementation.
     *
     * @return void
     * @since 0.1.0
     */
    public function testWrongDefaultProviderRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new LocaleNegotiatorFactory())($this->container([DefaultLocaleProvider::class => new \stdClass()]));
    }

    /**
     * Construct one operation's host container from explicit ports.
     *
     * @param array<string, mixed> $overrides Intentional host binding overrides.
     *
     * @return ServiceManager Independently configured operation container.
     * @since 0.1.0
     */
    private function container(array $overrides = []): ServiceManager
    {
        $provider = (new ConfigProvider())();
        $supported = new SupportedLocales();
        $services = [
            SupportedLocales::class => $supported,
            ActiveLocale::class => new ActiveLocale($supported),
            MessageCatalogueRepository::class => $this->createStub(MessageCatalogueRepository::class),
            MessageOverrideRepository::class => $this->createStub(MessageOverrideRepository::class),
            MessagePatternFormatter::class => new IntlMessagePatternFormatter(),
            DefaultLocaleProvider::class => new class implements DefaultLocaleProvider {
                public function locale(): LocaleTag
                {
                    return LocaleTag::fromString('de');
                }
            },
            'config' => $provider,
        ];

        return new ServiceManager(array_merge(
            $provider['dependencies'],
            ['services' => array_replace($services, $overrides)],
        ));
    }
}
