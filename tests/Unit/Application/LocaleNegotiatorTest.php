<?php

declare(strict_types=1);

namespace Kumwe\Localization\Tests\Unit\Application;

use Kumwe\Localization\Application\DefaultLocaleProvider;
use Kumwe\Localization\Application\LocaleNegotiator;
use Kumwe\Localization\Application\SupportedLocales;
use Kumwe\Localization\Domain\InvalidLocaleTag;
use Kumwe\Localization\Domain\LocaleTag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Preserves negotiation behavior while the settings-dependent tests remain with the host adapter.
 *
 * @since 0.1.0
 */
#[CoversClass(LocaleNegotiator::class)]
#[CoversClass(SupportedLocales::class)]
final class LocaleNegotiatorTest extends TestCase
{
    /**
     * Explicit selection, header quality and the host default retain their ordered precedence.
     *
     * @return void
     * @since 0.1.0
     */
    public function testNegotiationMatrixPreservesPrecedenceAndBounds(): void
    {
        $provider = new class implements DefaultLocaleProvider {
            public function locale(): LocaleTag
            {
                return LocaleTag::fromString('he');
            }
        };
        $negotiator = new LocaleNegotiator(new SupportedLocales(), $provider);
        foreach (
            [
            ['de', 'ar,en-GB;q=0.8', 'de'],
            [null, 'ja;q=0.9,ar;q=0.8,de;q=0.4', 'ar'],
            [null, 'de;q=0.5,af;q=0.5', 'de'],
            ['pt_PT', '', 'pt-BR'],
            [null, '', 'he'],
            [null, '*', 'he'],
            [null, 'ar;q=0', 'he'],
            ['not a locale', '', 'he'],
            ['ja', 'ja', 'he'],
            [null, str_repeat('en-GB,', 200), 'he'],
            ] as [$explicit, $header, $expected]
        ) {
            self::assertSame($expected, $negotiator->negotiate($explicit, $header)->toString());
        }
    }

    /**
     * Registry preference and source invariants remain unchanged.
     *
     * @return void
     * @since 0.1.0
     */
    public function testSupportedLocalesAndSource(): void
    {
        $supported = new SupportedLocales();
        self::assertSame(['en-GB', 'en-US', 'af', 'de', 'he', 'ar', 'es', 'pt-BR', 'zh-Hans'], $supported->tags());
        self::assertSame('en-GB', $supported->source()->toString());
        self::assertSame('en-GB', $supported->best(LocaleTag::fromString('en'))?->toString());
        self::assertSame('en-US', $supported->best(LocaleTag::fromString('en-US'))?->toString());
        self::assertTrue($supported->carries(LocaleTag::fromString('zh-Hans')));
        self::assertFalse($supported->carries(LocaleTag::fromString('zh-Hant')));
        self::assertNull($supported->best(LocaleTag::fromString('ja')));
        $this->expectException(InvalidLocaleTag::class);
        new SupportedLocales(['de'], 'en-GB');
    }

    /**
     * A provider failure propagates; fallback policy belongs to its host implementation.
     *
     * @return void
     * @since 0.1.0
     */
    public function testProviderFailureIsNotSilentlyConverted(): void
    {
        $provider = new class implements DefaultLocaleProvider {
            public function locale(): LocaleTag
            {
                throw new \RuntimeException('Host default unavailable');
            }
        };
        $this->expectException(\RuntimeException::class);
        (new LocaleNegotiator(new SupportedLocales(), $provider))->negotiate(null);
    }
}
