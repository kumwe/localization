# Localization ownership

`kumwe/localization` owns portable locale and message identifiers, ordered catalogues, translation,
ICU formatting, negotiation, override records and storage contracts. Its namespace is
`Kumwe\Localization\*`. No Kumwe package dependency is required.

The host owns trusted site/organization selection, authorization, administered mutations, default
settings and their failure policy, persistence/transactions, filesystem catalogue composition and
compilation, cache invalidation, middleware, Twig and delivery adapters. The package receives explicit
ports and operation context. It neither reads host configuration nor chooses authority.

Phase 1 preserves the 21 portable Domain/Application types at App commit
`960ce8ec00cf724a7cae03e5ba09c4852c9ab54e`, extracts the ICU formatter and its exception, and inverts
LocaleNegotiator's host dependency through DefaultLocaleProvider. Domain and Application suffixes are
preserved beneath the canonical namespace. No old namespace alias is provided.

The App compiler retains a Studio-specific placeholder-validation exception, while compiled catalogue
loading binds trusted executable PHP files and process cache policy. Both remain host adapters.

The package is Apache-2.0, targets PHP 8.5 with ext-intl, and uses SemVer. Extraction and release do not
complete App adoption or a functional roadmap objective. Maintainers merge; automation releases.
