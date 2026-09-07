<?php

declare(strict_types=1);

namespace Kumwe\Localization\Tests\Unit\Application;

use DateTimeImmutable;
use Kumwe\Localization\Application\MessageOverrideRecord;
use Kumwe\Localization\Domain\LocaleTag;
use Kumwe\Localization\Domain\MessageCatalogue;
use Kumwe\Localization\Domain\MessageCatalogueLayer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Pins portable record serialization and catalogue insertion order independently of persistence.
 *
 * @since 0.1.0
 */
#[CoversClass(MessageOverrideRecord::class)]
#[CoversClass(MessageCatalogue::class)]
final class RecordAndCatalogueTest extends TestCase
{
    /**
     * Preserve the exact stable response shape including Unicode, nulls and timezone offsets.
     *
     * @return void
     * @since 0.1.0
     */
    public function testOverrideSerialization(): void
    {
        $record = new MessageOverrideRecord(
            MessageCatalogueLayer::Site,
            'site-1',
            null,
            'de',
            'core.example.label',
            'Grüße',
            new DateTimeImmutable('2026-09-07T12:00:00+02:00'),
        );
        self::assertSame([
            'layer' => 'site', 'site' => 'site-1', 'organization' => null, 'locale' => 'de',
            'identifier' => 'core.example.label', 'pattern' => 'Grüße', 'updated_at' => '2026-09-07T12:00:00+02:00',
        ], $record->toArray());
    }

    /**
     * Catalogue values retain insertion order and distinguish missing from an empty pattern.
     *
     * @return void
     * @since 0.1.0
     */
    public function testCatalogueOrderAndEmptyValues(): void
    {
        $locale = LocaleTag::fromString('de');
        $catalogue = new MessageCatalogue($locale, MessageCatalogueLayer::Core, [
            'core.z.label' => '', 'core.a.label' => 'A',
        ]);
        self::assertSame(['core.z.label', 'core.a.label'], $catalogue->identifiers());
        self::assertSame(2, $catalogue->count());
        self::assertTrue($catalogue->has('core.z.label'));
        self::assertSame('', $catalogue->pattern('core.z.label'));
        self::assertSame(0, MessageCatalogue::empty($locale, MessageCatalogueLayer::Core)->count());
    }
}
