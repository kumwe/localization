<?php

declare(strict_types=1);

namespace Kumwe\Localization;

use Kumwe\Localization\Application\CatalogueTranslator;
use Kumwe\Localization\Application\LocaleNegotiator;
use Kumwe\Localization\Application\Translator;
use Kumwe\Localization\Container\CatalogueTranslatorFactory;
use Kumwe\Localization\Container\LocaleNegotiatorFactory;

/**
 * Declares only operation-scoped runtime services; host ports and contexts must be supplied explicitly.
 *
 * @since 0.1.0
 */
final class ConfigProvider
{
    /**
     * Return deterministic Mezzio container configuration without constructing any service.
     *
     * @return array{dependencies: array{factories: array<class-string,
     *         class-string<CatalogueTranslatorFactory>|class-string<LocaleNegotiatorFactory>>,
     *         aliases: array<class-string, class-string>, shared: array<class-string, bool>},
     *         kumwe: array{localization: array{maximum_accept_language_bytes: int}}}
     *
     * @since 0.1.0
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    CatalogueTranslator::class => CatalogueTranslatorFactory::class,
                    LocaleNegotiator::class => LocaleNegotiatorFactory::class,
                ],
                'aliases' => [Translator::class => CatalogueTranslator::class],
                'shared' => [CatalogueTranslator::class => false, LocaleNegotiator::class => false],
            ],
            'kumwe' => ['localization' => ['maximum_accept_language_bytes' => 512]],
        ];
    }
}
