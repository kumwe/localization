<?php

declare(strict_types=1);

namespace Kumwe\Localization\Tests\Unit\Application;

use Kumwe\Localization\Application\MessageOverrideRepository;
use Kumwe\Localization\Application\MessageOverrideStore;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * The package owns its storage port's explicit scope contract; persistence behavior is adapter-owned.
 * @since 0.1.1
 */
#[CoversClass(MessageOverrideStore::class)]
#[CoversClass(MessageOverrideRepository::class)]
final class StoragePortTest extends TestCase
{
    /**
     * Mutation keeps the site, organization, locale and identifier explicit and never broadens the read port.
     * @return void
     * @since 0.1.1
     */
    public function testMutationPortKeepsScopeExplicitAndSeparateFromTheReadPort(): void
    {
        $store = new ReflectionClass(MessageOverrideStore::class);
        $reader = new ReflectionClass(MessageOverrideRepository::class);
        self::assertTrue($store->isInterface());
        self::assertTrue($reader->isInterface());
        self::assertSame([], $store->getInterfaceNames());
        self::assertSame([], $reader->getInterfaceNames());
        $parameters = [];
        foreach ($store->getMethods() as $method) {
            $parameters[$method->getName()] = array_map(
                static fn (\ReflectionParameter $parameter): string => $parameter->getName(),
                $method->getParameters(),
            );
        }
        self::assertSame([
            'lockSite' => ['site'],
            'overrides' => ['layer', 'site', 'organization', 'locale'],
            'put' => ['override'],
            'remove' => ['layer', 'site', 'organization', 'locale', 'identifier'],
        ], $parameters);
        self::assertSame(['siteOverrides', 'organizationOverrides'], array_map(
            static fn (\ReflectionMethod $method): string => $method->getName(),
            $reader->getMethods(),
        ));
        self::assertFalse($reader->hasMethod('put'));
        self::assertFalse($reader->hasMethod('lockSite'));
    }
}
