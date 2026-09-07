<?php

declare(strict_types=1);

namespace Kumwe\Localization\Tests\Unit\Application;

use Kumwe\Localization\Application\ActiveLocale;
use Kumwe\Localization\Application\SupportedLocales;
use Kumwe\Localization\Application\TranslationScope;
use Kumwe\Localization\Domain\LocaleTag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Keep portable locale holder transitions in the package; trusted scope resolution stays with the host.
 * @since 0.1.1
 */
#[CoversClass(ActiveLocale::class)]
#[CoversClass(TranslationScope::class)]
final class ActiveLocaleBoundaryTest extends TestCase
{
    /**
     * Language-bearing resources preserve the already established scope and unit-of-work identity.
     * @return void
     * @since 0.1.1
     */
    public function testAdoptingLanguageAndScopePreservesTheOtherCoordinate(): void
    {
        $active = new ActiveLocale(new SupportedLocales(['de', 'he'], 'de'));
        $site = new TranslationScope('site-a');
        $organization = new TranslationScope('site-a', 'organization-a');
        $active->begin(LocaleTag::fromString('de'), $site);
        $generation = $active->generation();
        $active->adoptLocale(LocaleTag::fromString('he'));
        self::assertSame($site, $active->scope());
        self::assertSame('he', $active->locale()->toString());
        self::assertSame($generation, $active->generation());
        $active->adoptScope($organization);
        self::assertSame($organization, $active->scope());
        self::assertSame('he', $active->locale()->toString());
        self::assertSame($generation, $active->generation());
        self::assertSame('site-a', $site->key());
        self::assertSame('site-a/organization-a', $organization->key());
    }

    /**
     * Ending and replacing units of work removes both coordinates, including scope-less starts.
     * @return void
     * @since 0.1.1
     */
    public function testUnitOfWorkEdgesInvalidateSnapshotsAndRestoreExplicitDefaults(): void
    {
        $active = new ActiveLocale(new SupportedLocales(['de', 'he'], 'de'));
        self::assertSame(0, $active->generation());
        self::assertSame('de', $active->locale()->toString());
        self::assertSame('default', $active->scope()->key());
        $active->begin(LocaleTag::fromString('he'), new TranslationScope('site-a', 'organization-a'));
        self::assertSame(1, $active->generation());
        $active->end();
        self::assertSame(2, $active->generation());
        self::assertSame('de', $active->locale()->toString());
        self::assertNull($active->scope()->organization);
        self::assertSame('default', $active->scope()->site);
        $active->begin(LocaleTag::fromString('he'));
        self::assertSame(3, $active->generation());
        self::assertSame('default', $active->scope()->key());
        $active->begin(LocaleTag::fromString('de'), new TranslationScope('site-b'));
        self::assertSame(4, $active->generation());
        self::assertSame('site-b', $active->scope()->key());
        self::assertSame('de', $active->locale()->toString());
    }
}
