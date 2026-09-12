# Architecture and ownership boundary

Domain values have no inward dependency. Application behavior depends on Domain and narrow ports.
The ICU infrastructure formatter depends on those contracts and PHP's intl extension. Only Container
factories depend on PSR-11; the deterministic provider returns configuration without reading host state.

The package exposes eight Domain types, thirteen Application types, two ICU implementation/error types,
DefaultLocaleProvider and three DI types: 27 exported types in total. DefaultLocaleProvider separates
negotiation from host settings. The source parity corpus records historical implementation provenance
at Core commit `960ce8ec00cf724a7cae03e5ba09c4852c9ab54e` and remains an executable compatibility gate.

A Core SiteDefaultLocale adapter implements DefaultLocaleProvider. Its settings
read caching and language-degradation policy remain unchanged. LocaleNegotiator's constructor now accepts
that interface; all negotiation/quality/fallback behavior is unchanged. This is the sole constructor
dependency boundary recorded as LOCALIZATION-001 in the release evidence.

MessageOverrideService retains authorization, clock, audit, transaction and quota policy. Doctrine
repositories and migrations, middleware, Twig, CLI and browser behavior stay in App. MessageCatalogueCompiler
contains the App-owned Studio shell placeholder exception and remains alongside XLIFF build tooling.
CompiledMessageCatalogueRepository retains filesystem loading, trusted executable cache and process cache
configuration. ArrayMessageOverrideRepository is retained in App and appears in this package only as a
test fixture, excluded from the runtime archive. No vendor source is copied and no old namespace is aliased.

Provider services are non-shared because their dependencies include scoped host state. ActiveLocale must
be created once for an operation, shared only inside that operation, and closed in finally. Its default
source locale/default scope and generation cache invalidation preserve current sequential-worker behavior.
A shared mutable context provides no fiber/thread safety guarantee.

The package owns its portable class tests; App retains tests for the authorized settings/override services,
settings-store failure/caching, Doctrine persistence, compiler/file adapters, middleware, Twig, HTTP/CLI
wording and browser behavior. App adoption removes duplicate unit ownership only after release verification.
