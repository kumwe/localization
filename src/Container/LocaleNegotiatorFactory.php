<?php

declare(strict_types=1);

namespace Kumwe\Localization\Container;

use InvalidArgumentException;
use Kumwe\Localization\Application\DefaultLocaleProvider;
use Kumwe\Localization\Application\LocaleNegotiator;
use Kumwe\Localization\Application\SupportedLocales;
use Psr\Container\ContainerInterface;

/**
 * Constructs negotiation with a host default provider and explicitly bounded header parsing.
 *
 * @since 0.1.0
 */
final class LocaleNegotiatorFactory
{
    /**
     * Resolve only localization services and the package's own options.
     *
     * @param ContainerInterface $container Host container for the current host scope.
     *
     * @return LocaleNegotiator A fresh, stateless negotiator.
     *
     * @throws InvalidArgumentException When services or the positive integer header limit are invalid.
     *
     * @since 0.1.0
     */
    public function __invoke(ContainerInterface $container): LocaleNegotiator
    {
        $supported = $container->get(SupportedLocales::class);
        $default = $container->get(DefaultLocaleProvider::class);
        $config = $container->has('config') ? $container->get('config') : [];
        if (!is_array($config)) {
            throw new InvalidArgumentException('Localization configuration must be an array.');
        }
        $kumwe = $config['kumwe'] ?? [];
        if (!is_array($kumwe)) {
            throw new InvalidArgumentException('kumwe configuration must be an array.');
        }
        $options = $kumwe['localization'] ?? [];
        if (!is_array($options)) {
            throw new InvalidArgumentException('kumwe.localization configuration must be an array.');
        }
        $maximum = $options['maximum_accept_language_bytes'] ?? 512;
        if (!is_int($maximum) || $maximum < 1) {
            throw new InvalidArgumentException('maximum_accept_language_bytes must be a positive integer.');
        }
        if (!$supported instanceof SupportedLocales || !$default instanceof DefaultLocaleProvider) {
            throw new InvalidArgumentException(
                'Localization negotiation dependencies must match their service contracts.',
            );
        }

        return new LocaleNegotiator($supported, $default, $maximum);
    }
}
