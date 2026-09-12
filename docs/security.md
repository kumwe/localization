# Security and compatibility

Report suspected vulnerabilities privately to the maintainers through the repository's GitHub security
reporting facility when enabled; otherwise contact the repository owner privately before public disclosure.
Do not include customer wording, credentials or full hostile payloads in public issue reports.

LocaleTag accepts only a bounded language/script/region grammar after trim/underscore normalization.
MessageIdentifier accepts at most 190 bytes and at least three dotted lowercase segments; diagnostic
exception quoting is bounded. Header negotiation defaults to 512 bytes and drops oversize inputs.
The package intentionally preserves existing quality parsing and missing-message semantics.

Catalogues, translation scopes and override records receive typed trusted data; they are not authorization
or database-write validators. Hosts enforce map quotas, valid scope identifiers and membership before
constructing them. Scope key components must be unambiguous under the preserved slash-delimited format.
ActiveLocale and translator caches belong to one non-overlapping operation and require finally cleanup.

ICU is required explicitly. Missing intl refuses formatter construction; there is no substitution fallback.
Malformed patterns and formatting failures raise MessageFormattingFailed. ICU owns Unicode/plural/number
data, so consistent bytes require matching ICU versions; date output additionally depends on the host
timezone defaults. Formatted dates therefore depend on the configured deployment environment.

Filesystem/XLIFF/compiler adapters and executable compiled catalogue loading remain host responsibilities.
Do not pass untrusted writable paths to those retained App adapters. This package opens no database,
network connection, transaction or filesystem catalogue and owns no secret or final authorization policy.
