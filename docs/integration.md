# Host integration

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

App Phase 2 requires a human-merged release and an independent external release attestation. Change all
inventoried imports from Kumwe\App\Localization to Kumwe\Localization for the extracted symbols only;
add `implements DefaultLocaleProvider` to retained SiteDefaultLocale; register its instance under that
interface in the host container. Do not rename retained middleware/services/storage/compiler adapters.
Regenerate Composer state, remove the mapped old definitions and duplicate unit tests, and run the full
affected App/database/delivery/browser suites. The handoff contains the exact file inventory and drift check.
