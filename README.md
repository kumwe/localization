# Kumwe Localization

[![Packagist version][version-badge]][package]
[![CI][ci-badge]][ci]
[![PHP requirement][php-badge]](composer.json)
[![License][license-badge]](LICENSE)

[version-badge]: https://img.shields.io/packagist/v/kumwe/localization
[package]: https://packagist.org/packages/kumwe/localization
[ci-badge]: https://github.com/kumwe/localization/actions/workflows/ci.yml/badge.svg?branch=main
[ci]: https://github.com/kumwe/localization/actions/workflows/ci.yml?query=branch%3Amain
[php-badge]: https://img.shields.io/packagist/dependency-v/kumwe/localization/php
[license-badge]: https://img.shields.io/packagist/l/kumwe/localization

Portable locale negotiation, layered message catalogues and ICU formatting.
Canonical namespace: `Kumwe\Localization\*`. Requires PHP 8.5 and ext-intl. Apache-2.0.

The package owns translation behavior and contracts; the host supplies catalogues, override storage,
trusted scope and its default locale. Authorization, settings mutation, persistence and delivery remain
in the host. No Kumwe dependency or historical namespace alias is used.

## Installation and usage

Install the published release with an exact pre-1.0 pin:

```bash
composer require kumwe/localization:0.1.1
```

Review [release verification](docs/releasing.md) and compatible PHP/ICU versions before upgrading.

```php
use Kumwe\Localization\Domain\LocaleTag;
use Kumwe\Localization\Infrastructure\IntlMessagePatternFormatter;

$locale = LocaleTag::fromString('pt_br');
echo $locale->toString(); // pt-BR
echo (new IntlMessagePatternFormatter())->format('Olá, {name}!', ['name' => 'Kumwe'], $locale);
```

Run `php examples/translate.php` after `composer install` for a complete catalogue/override/default-provider
example. It prints `Hello, Kumwe!`. `examples/container.php` proves the same behavior with an explicitly
configured Laminas ServiceManager. Full public member contracts are in [docs/public-api.md](docs/public-api.md).

## Core composition

Register `Kumwe\Localization\ConfigProvider` explicitly in the host ConfigAggregator provider list.
It registers only `CatalogueTranslator` and `LocaleNegotiator`, both **non-shared**, plus the canonical
`Translator` interface binding. Supply `SupportedLocales`, one operation's `ActiveLocale`,
`MessageCatalogueRepository`, `MessageOverrideRepository`, `MessagePatternFormatter` and
`DefaultLocaleProvider` explicitly. Values and stateless ICU formatting require no container factory.
`kumwe.localization.maximum_accept_language_bytes` defaults to 512 and must be a positive integer.
See [docs/integration.md](docs/integration.md) for composition and lifetime rules.

Identifiers are stable dotted names, not prose. Resolution tries the requested locale and its fallbacks,
then the source locale; within each locale organization overrides site, extension and core. A missing
identifier returns itself. Invalid identifiers and malformed ICU patterns throw explicit exceptions.
Catalogues preserve insertion order; host compilation decides sorted byte order. ICU data/version and
timezone affect locale-sensitive formatted output and must be pinned by the deployment when byte parity
is required. No binary floating-point financial algorithm or transaction manager is introduced.

`ActiveLocale` is mutable operation context. `CatalogueTranslator` memoizes only within its generation.
Open/close sequential operations with `begin()` and `end()` in `finally`; allocate separate context and
translator instances for overlapping requests/fibers. Host adapters supply bounded catalogue maps and
trusted unambiguous scope identifiers; these DTOs are not authorization or content-validation boundaries.

## Development

Run `composer install`, then `composer check` for lint, member documentation, architecture, manifests,
static analysis, PSR-12,
unit tests, security audit and a fresh no-dev authoritative-classmap consumer installed from the built ZIP.
PHP 8.5 is the supported CI lane. `ext-intl` and `psr/container` are the only non-PHP runtime dependencies;
the latter is used solely by factories. `ext-zip` and Laminas ServiceManager are verification dependencies.

[Release evidence](docs/release-record.md) preserves source mappings, manifest identities and consumer obligations.
[docs/releasing.md](docs/releasing.md) specifies publication and independent verification gates;
[docs/security.md](docs/security.md) records limits, deployment assumptions and private reporting.
