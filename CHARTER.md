# Localization ownership

`kumwe/localization` owns portable locale and message identifiers, ordered catalogues, translation,
ICU formatting, negotiation, override records and storage contracts. Its namespace is
`Kumwe\Localization\*`. No Kumwe package dependency is required.

The host owns trusted site/organization selection, authorization, administered mutations, default
settings and their failure policy, persistence/transactions, filesystem catalogue composition and
compilation, cache invalidation, middleware, Twig and delivery adapters. The package receives explicit
ports and operation context. It neither reads host configuration nor chooses authority.

Domain and Application contracts retain their canonical namespace suffixes. The package supplies 27
public types, including the ICU formatter, DefaultLocaleProvider and explicit container factories.
Locale negotiation receives the default locale through that port; host settings policy remains outside
the package. No old namespace alias is provided.

The App compiler retains a Studio-specific placeholder-validation exception, while compiled catalogue
loading binds trusted executable PHP files and process cache policy. Both remain host adapters.

The package is Apache-2.0, targets PHP 8.5 with ext-intl, and uses SemVer. Core verifies installed
package and PHP/ICU compatibility independently of publication. See [integration](docs/integration.md).
