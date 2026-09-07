# Architecture and extraction boundary

Domain values have no inward dependency. Application behavior depends on Domain and narrow ports.
The ICU infrastructure formatter depends on those contracts and PHP's intl extension. Only Container
factories depend on PSR-11; the deterministic provider returns configuration without reading host state.

The source snapshot is App `960ce8ec00cf724a7cae03e5ba09c4852c9ab54e`. The brief's 21 candidates are all
eight Domain types and thirteen Application types after retaining MessageOverrideService and
SiteDefaultLocale. Two ICU implementation/error types complete formatting; DefaultLocaleProvider breaks
the negotiation-to-host-settings dependency. Three DI types make 27 exported types in total.

SiteDefaultLocale remains in App and will implement DefaultLocaleProvider when adopted. Its settings
read caching and language-degradation policy remain unchanged. LocaleNegotiator's constructor now accepts
that interface; all negotiation/quality/fallback behavior is unchanged. This is the sole constructor
dependency break beyond the namespace movement, recorded as decision LOCALIZATION-001 in the handoff.

MessageOverrideService retains authorization, clock, audit, transaction and quota policy. Doctrine
repositories and migrations, middleware, Twig, CLI and browser behavior stay in App. MessageCatalogueCompiler
contains the App-owned Studio shell placeholder exception and remains alongside XLIFF build tooling.
CompiledMessageCatalogueRepository retains filesystem loading, trusted executable cache and process cache
configuration. ArrayMessageOverrideRepository is retained in App and appears in this package only as a
test fixture, excluded from the runtime archive. No vendor source is copied and no old namespace is aliased.

Provider services are non-shared because their dependencies include scoped host state. ActiveLocale must
be created once for an operation, shared only inside that operation, and closed in finally. Its default
source locale/default scope and generation cache invalidation preserve current sequential-worker behavior.
This extraction does not claim fiber/thread safety for a shared mutable context.

The package owns its moved class tests; App retains tests for the authorized settings/override services,
settings-store failure/caching, Doctrine persistence, compiler/file adapters, middleware, Twig, HTTP/CLI
wording and browser behavior. App adoption removes duplicate unit ownership only after release verification.
