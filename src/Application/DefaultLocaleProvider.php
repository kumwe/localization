<?php

declare(strict_types=1);

namespace Kumwe\Localization\Application;

use Kumwe\Localization\Domain\LocaleTag;

/**
 * Supplies the host-resolved default without giving negotiation access to site settings.
 *
 * Implementations return a carried locale and own their settings/cache/failure policy. A provider is
 * supplied for the operation or host scope it describes; it never selects another site's authority.
 *
 * @since 0.1.0
 */
interface DefaultLocaleProvider
{
    /**
     * Return the already resolved default for this host scope.
     *
     * @return LocaleTag A locale carried by the associated SupportedLocales registry.
     *
     * @since 0.1.0
     */
    public function locale(): LocaleTag;
}
