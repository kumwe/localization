# Core contract and host integration

Use `examples/translate.php` for direct construction and `examples/container.php` for a complete standalone
Laminas host. Register `Kumwe\Localization\ConfigProvider::class` explicitly in ConfigAggregator; merge its
`dependencies` into ServiceManager. Automatic discovery is deliberately not enabled.

| Service | Host responsibility | Lifetime |
| --- | --- | --- |
| SupportedLocales | Construct from an explicit supported/source list; existing nine-language defaults remain | Immutable, may be shared |
| ActiveLocale | Construct from that registry; begin/end the current locale and trusted TranslationScope | One per operation |
| MessageCatalogueRepository | Serve core/extension maps for exact locales, never apply locale fallbacks itself | Host adapter policy |
| MessageOverrideRepository | Serve bounded maps for the exact trusted site/organization and locale | Host adapter policy |
| MessagePatternFormatter | Supply IntlMessagePatternFormatter or a contract-conforming explicit formatter | Stateless ICU instance may be shared |
| DefaultLocaleProvider | Return a carried default locale; own settings reads/caching/failure policy | Scope-specific host provider |
| CatalogueTranslator | Constructed by CatalogueTranslatorFactory; Translator aliases this concrete service | Non-shared |
| LocaleNegotiator | Constructed by LocaleNegotiatorFactory from supported/default ports and package options | Non-shared |

Both factories retrieve canonical service IDs, validate service types and throw InvalidArgumentException
on invalid bindings. Container resolution exceptions propagate. LocaleNegotiatorFactory accepts only an
array configuration tree and a positive integer `kumwe.localization.maximum_accept_language_bytes` (512
by default). Direct construction preserves the original constructor behavior and optional limit.

Do not put an operation's ActiveLocale into a process-global singleton when requests can overlap. Resolve
and inject one translator within the operation's container. For sequential workers, begin/end always
increments the memoization generation, preventing stale wording snapshots from crossing requests.

## Compatibility and test ownership

Use an exact independently verified package release and preserve the host's settings, degradation and
cache behavior. Bind the retained SiteDefaultLocale adapter to DefaultLocaleProvider before constructing
LocaleNegotiator. Middleware, services, storage and compiler adapters remain under Core ownership.

When replacing historical types, review current consumers against the source mappings in
[release evidence](release-record.md). Regenerate Composer state and capability indexes through their
supported tools. Remove a duplicate class test together with its retired implementation after verified
replacement, and split mixed negotiation tests to retain host settings-failure and caching assertions.

Core retains persistence, middleware, Twig, compiled catalogue, integration, functional and database/
delivery/browser tests. Package tests own portable locale, catalogue, negotiation and formatting behavior.
Verify operation-context isolation and the actual container bindings as part of Core integration.
