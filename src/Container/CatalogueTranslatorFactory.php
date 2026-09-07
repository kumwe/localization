<?php

declare(strict_types=1);

namespace Kumwe\Localization\Container;

use InvalidArgumentException;
use Kumwe\Localization\Application\ActiveLocale;
use Kumwe\Localization\Application\CatalogueTranslator;
use Kumwe\Localization\Application\MessageCatalogueRepository;
use Kumwe\Localization\Application\MessageOverrideRepository;
use Kumwe\Localization\Application\MessagePatternFormatter;
use Kumwe\Localization\Application\SupportedLocales;
use Psr\Container\ContainerInterface;

/**
 * Constructs a translator from explicit host ports and the context of one operation.
 *
 * @since 0.1.0
 */
final class CatalogueTranslatorFactory
{
    /**
     * Resolve the five documented collaborators without retaining the container.
     *
     * @param ContainerInterface $container Host container for this operation.
     *
     * @return CatalogueTranslator A fresh translator with an initially empty catalogue cache.
     *
     * @throws InvalidArgumentException When a service does not implement its declared contract.
     *
     * @since 0.1.0
     */
    public function __invoke(ContainerInterface $container): CatalogueTranslator
    {
        $catalogues = $container->get(MessageCatalogueRepository::class);
        $overrides = $container->get(MessageOverrideRepository::class);
        $formatter = $container->get(MessagePatternFormatter::class);
        $active = $container->get(ActiveLocale::class);
        $supported = $container->get(SupportedLocales::class);
        if (
            !$catalogues instanceof MessageCatalogueRepository
            || !$overrides instanceof MessageOverrideRepository
            || !$formatter instanceof MessagePatternFormatter
            || !$active instanceof ActiveLocale
            || !$supported instanceof SupportedLocales
        ) {
            throw new InvalidArgumentException(
                'Localization translator dependencies must match their service contracts.',
            );
        }

        return new CatalogueTranslator($catalogues, $overrides, $formatter, $active, $supported);
    }
}
