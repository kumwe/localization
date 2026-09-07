# Public API

All types are under `Kumwe\Localization`. Parameter and return types below are canonical; source PHPDoc
provides collection element shapes, constraints and exception details. No operation opens a transaction.
Values and enums are immutable. Ports inherit side effects from their explicitly supplied implementations.
Factories may resolve host services but never retain the container. No API authorizes a site or organization.

ICU output follows the installed ICU version; byte parity requires the same ICU data/version and explicit
locale. Date formatting also uses ICU/PHP timezone defaults, preserved from the extracted implementation.
Host deployment must pin ICU and set its intended timezone for repeatable date output.

`ActiveLocale` and `CatalogueTranslator` are operation-scoped and unsafe for overlapping fibers/requests.
Reuse sequentially only with begin/end in finally; no process-global service may capture these contexts.
`TranslationScope::key()` preserves the slash-delimited source format: the host supplies validated stable
identifiers and must avoid ambiguous slash-containing scope components. Records/catalogues do not validate
their array contents at construction; callers supply the documented typed maps. No new bounds are invented.

## `Kumwe\Localization\Application\ActiveLocale`

Request-scoped holder for the locale and translation scope the unit of work in flight resolved.

A Twig function receives only its arguments, and a console command receives only its input, so
the locale a middleware negotiated has to reach them through something the container shares. This
is that something, and it is deliberately the same shape as `CorrelationContext`: the middleware
opens a unit of work on it, everything rendered while it is open reads the same locale, and the
middleware closes it again in a `finally` so a long-lived worker never carries one request's
language into the next one's output.

Nothing resolves a locale here. It holds what `LocaleNegotiator` decided and nothing else, and it
answers with the source locale outside a unit of work so a boot-time render is never locale-less.

@since  2.0.0

### `__construct(Kumwe\Localization\Application\SupportedLocales $supported)`

Bind the holder to the locale it answers with outside a unit of work.

@param  SupportedLocales  $supported  Registry supplying the source locale as the standing default.

@since  2.0.0

### `adoptLocale(Kumwe\Localization\Domain\LocaleTag $locale)` → `void`

Adopt the locale named by a language-bearing resource while preserving its trusted scope.

Negotiation decides language-neutral entry points. A resolved content path already names one
locale, so delivery calls this before rendering to keep the document language, translated chrome
and content together without discarding the site or organization override scope.

@param   LocaleTag  $locale  Locale the resolved resource declares.

@return  void

@since   2.0.0

### `adoptScope(Kumwe\Localization\Application\TranslationScope $scope)` → `void`

Replace the override scope after authentication resolves trusted organization membership.

Locale negotiation deliberately runs before authentication, so it can only open a site scope.
The later scope middleware calls this with the authenticated execution context while leaving the
already-negotiated locale untouched.

@param   TranslationScope  $scope  Trusted site and optional organization for downstream lookups.

@return  void

@since   2.0.0

### `begin(Kumwe\Localization\Domain\LocaleTag $locale, ?Kumwe\Localization\Application\TranslationScope $scope (optional))` → `void`

Open a unit of work, replacing whatever the previous one left behind.

@param   LocaleTag          $locale  Locale this unit of work renders in.
@param   ?TranslationScope  $scope   Site and organization whose overrides apply, or null for the default.

@return  void

@since   2.0.0

### `end()` → `void`

Close the unit of work so nothing carries into the next one.

@return  void

@since   2.0.0

### `generation()` → `int`

Identify the locale unit of work whose catalogue chains may be reused.

@return  int  Value that changes whenever a request locale is opened or closed.

@since   2.0.0

### `locale()` → `Kumwe\Localization\Domain\LocaleTag`

The locale in flight.

@return  LocaleTag  The negotiated locale, or the source locale outside a unit of work.

@since   2.0.0

### `scope()` → `Kumwe\Localization\Application\TranslationScope`

The override scope in flight.

@return  TranslationScope  The negotiated scope, or the default site outside a unit of work.

@since   2.0.0

## `Kumwe\Localization\Application\CatalogueTranslator`

Resolves a message through the four-layer override chain and formats it with ICU MessageFormat.

Lookup walks two axes. The outer one is the locale and its fallbacks — `pt-BR`, then `pt`, then
the source locale — and the inner one is the override chain, organization before site before
extension before core. A locale-specific override therefore beats a source-locale core message,
which is what an operator expects when they change a word for one language only.

A message no layer carries comes back as its own identifier. That is deliberate and is the
difference between an interface that is visibly missing a translation and one that is silently
blank: the first is a defect anybody can see and report, the second is a defect nobody notices
until a customer does.

Each locale-and-scope pair is assembled once per active unit of work, so a page that resolves several
hundred messages performs one catalogue load rather than several hundred. `ActiveLocale` generations
bound that memoization even when the shared container and translator serve several sequential requests.

@since  2.0.0

### `__construct(Kumwe\Localization\Application\MessageCatalogueRepository $catalogues, Kumwe\Localization\Application\MessageOverrideRepository $overrides, Kumwe\Localization\Application\MessagePatternFormatter $formatter, Kumwe\Localization\Application\ActiveLocale $active, Kumwe\Localization\Application\SupportedLocales $supported)`

Bind the translator to its catalogue sources, its formatter and the locale in flight.

@param  MessageCatalogueRepository  $catalogues  Source of the core and extension layers.
@param  MessageOverrideRepository   $overrides   Source of the site and organization layers.
@param  MessagePatternFormatter     $formatter   ICU formatter every resolved pattern goes through.
@param  ActiveLocale                $active      Holder of the locale and scope of the unit of work.
@param  SupportedLocales            $supported   Registry supplying the source locale as last resort.

@since  2.0.0

### `attribution(string $identifier, ?Kumwe\Localization\Domain\LocaleTag $locale (optional))` → `?array`

Which layer answers for an identifier at a locale, and at which locale it was found.

An administration surface showing an operator why a word reads the way it does needs both
halves of the answer, and so does a test proving the chain resolves in the declared order.

@param   string      $identifier  Stable message identifier.
@param   ?LocaleTag  $locale      Locale to attribute, or null for the locale in flight.

@return  ?array{layer: MessageCatalogueLayer, locale: string}  The winning layer and the locale
         it was found at, or null when no layer carries the identifier.

@since   2.0.0

### `has(string $identifier, ?Kumwe\Localization\Domain\LocaleTag $locale (optional))` → `bool`

Whether any layer of the chain carries a message for an identifier at a locale.

@param   string      $identifier  Stable message identifier.
@param   ?LocaleTag  $locale      Locale to test, or null for the locale in flight.

@return  bool  True when a pattern exists at this locale or one of its fallbacks.

@throws  \Kumwe\Localization\Domain\InvalidMessageIdentifier  When the identifier does not
         satisfy the frozen grammar.

@since   2.0.0

### `translate(string $identifier, array $parameters (optional), ?Kumwe\Localization\Domain\LocaleTag $locale (optional))` → `string`

Resolve and format one message.

@param   string                                                   $identifier  Stable message identifier.
@param   array<string, string|int|float|bool|\DateTimeInterface>  $parameters  Values the ICU pattern
         names, keyed by placeholder name.
@param   ?LocaleTag                                               $locale      Locale to render in, or
         null for the locale of the unit of work in flight.

@return  string  The formatted message, or the identifier itself when no layer carries it.

@throws  \Kumwe\Localization\Domain\InvalidMessageIdentifier  When the identifier does not
         satisfy the frozen grammar.
@throws  MessageFormattingFailed  When the resolved pattern is not valid ICU MessageFormat, or
         the supplied parameters cannot satisfy it.

@since   2.0.0

## `Kumwe\Localization\Application\DefaultLocaleProvider`

Supplies the host-resolved default without giving negotiation access to site settings.

Implementations return a carried locale and own their settings/cache/failure policy. A provider is
supplied for the operation or host scope it describes; it never selects another site's authority.

@since 0.1.0

### `locale()` → `Kumwe\Localization\Domain\LocaleTag`

Return the already resolved default for this host scope.

@return LocaleTag A locale carried by the associated SupportedLocales registry.

@since 0.1.0

## `Kumwe\Localization\Application\LocaleNegotiator`

Decides which of the carried locales a unit of work renders in.

Three inputs are consulted, in this order, and the first that names a carried locale wins: an
explicit choice the caller made, the languages the caller's client says it accepts, and the
site's `default_locale` setting. The order is what makes each input mean something — an explicit
choice is a decision and must not be overruled by a browser preference, a browser preference is
better than a guess, and the site setting is the operator's answer for everyone who expressed no
preference at all. When none of the three names a carried locale the source locale is used, so
negotiation always produces a locale and never a null a caller has to handle.

Nothing here reads process state and nothing here writes any. The result is returned to the
caller, which is what keeps two units of work in one long-lived worker from sharing a language.

@since  2.0.0

### `__construct(Kumwe\Localization\Application\SupportedLocales $supported, Kumwe\Localization\Application\DefaultLocaleProvider $siteDefault, int $maximumHeader (optional))`

Bind negotiation to the carried locales and the site's administered default.

@param  SupportedLocales   $supported      Registry every candidate is reduced against.
@param  DefaultLocaleProvider  $siteDefault    Consumer of the site's `default_locale` setting.
@param  int                $maximumHeader  Longest `Accept-Language` header that will be parsed,
        in bytes; a longer one is ignored rather than walked.

@since  2.0.0

### `negotiate(?string $explicit, string $acceptLanguage (optional))` → `Kumwe\Localization\Domain\LocaleTag`

Resolve the locale for one unit of work.

@param   ?string  $explicit        Locale the caller asked for outright, or null when it asked
         for none. An unparseable or uncarried value is ignored rather than refused, because a
         stale bookmark must not turn a page into an error.
@param   string   $acceptLanguage  Raw `Accept-Language` header, empty when absent.

@return  LocaleTag  A locale this installation carries.

@since   2.0.0

## `Kumwe\Localization\Application\MessageCatalogueRepository`

Port supplying the two file-shipped layers of the chain: what core ships and what extensions ship.

These layers are build output rather than administered state, so an implementation reads them
from compiled PHP and never parses XLIFF: the authored format is for translators and translation
platforms, and the request path sees only an array the opcode cache already holds. A caller asks
for one layer at one locale and receives an empty catalogue rather than null when nothing is
shipped, so the resolver walks a uniform chain.

@since  2.0.0

### `catalogue(Kumwe\Localization\Domain\MessageCatalogueLayer $layer, Kumwe\Localization\Domain\LocaleTag $locale)` → `Kumwe\Localization\Domain\MessageCatalogue`

Read one file-shipped layer for one locale.

@param   MessageCatalogueLayer  $layer   Either the core layer or the extension layer.
@param   LocaleTag              $locale  Exact locale to read; fallback is the resolver's job.

@return  MessageCatalogue  The layer's messages, empty when it ships none at this locale.

@throws  \InvalidArgumentException  When asked for a layer this port does not serve.

@since   2.0.0

## `Kumwe\Localization\Application\MessageFormattingFailed`

Raised when a resolved pattern cannot be rendered with the values a caller supplied.

A pattern that does not compile is a defect in a catalogue, and a parameter bag that cannot
satisfy one is a defect in a call site. Both are reported rather than swallowed: rendering the
raw pattern instead would put `{count, plural, one {...} other {...}}` in front of an operator,
and rendering an empty string would hide the fault until someone noticed a blank label. The
message names the identifier and the locale so the failing catalogue entry can be found without
reproducing the request.

@since  2.0.0

### `pattern(string $locale, string $reason, string $context)` → `Kumwe\Localization\Application\MessageFormattingFailed`

State that the intl extension could not compile or apply a pattern.

@param   string  $locale   Locale the pattern was being rendered for.
@param   string  $reason   Diagnostic the intl extension reported.
@param   string  $context  Message identifier the pattern came from, or a description of the
         caller when the pattern was supplied directly.

@return  self  Exception naming the locale, the source of the pattern and the diagnostic.

@since   2.0.0

## `Kumwe\Localization\Application\MessageOverrideRecord`

One stored override, with the bookkeeping an administration screen needs and the render path does not.

The render path reads a bare identifier-to-pattern map, because that is all a lookup needs and
anything more would be paid for on every page. An operator deciding whether to keep a change needs
more than that: which layer carries it, which locale it applies to, and when it was last written.
This is that view, and it exists separately for exactly that reason rather than widening the map
the chain is assembled from.

@since  2.0.0

Public immutable data: `$identifier` (`string`), `$layer` (`Kumwe\Localization\Domain\MessageCatalogueLayer`),
`$locale` (`string`), `$organization` (`?string`), `$pattern` (`string`), `$site` (`string`), `$updatedAt`
(`DateTimeImmutable`). Constructor/factory contracts below specify each field.

### `__construct(Kumwe\Localization\Domain\MessageCatalogueLayer $layer, string $site, ?string $organization, string $locale, string $identifier, string $pattern, DateTimeImmutable $updatedAt)`

Describe one stored override.

@param  MessageCatalogueLayer  $layer         Which administered layer stores it; only `Site` and
        `Organization` are storable, because core and extension wording ships in files.
@param  string                 $site          Site the override belongs to.
@param  ?string                $organization  Organization within that site, or null at site level.
@param  string                 $locale        Canonical tag of the locale the override applies to.
@param  string                 $identifier    Message identifier the override replaces.
@param  string                 $pattern       ICU pattern rendered in place of the layer below.
@param  DateTimeImmutable      $updatedAt     Instant the override was last written.

@since  2.0.0

### `toArray()` → `array`

Flatten the record for a template or a JSON response.

@return  array{
             layer: string,
             site: string,
             organization: ?string,
             locale: string,
             identifier: string,
             pattern: string,
             updated_at: string
         } The record with its layer as its stored value and its instant in RFC 3339 form.

@since   2.0.0

## `Kumwe\Localization\Application\MessageOverrideRepository`

Port supplying the two administered layers of the chain: a site's wording and an organization's.

These are the layers an operator owns. They exist so that changing one word — relabelling
"Client" as "Patient" for a health vertical, as "Learner" for education, as "Guest" for
hospitality — is an administrative act rather than a fork of a catalogue, in one language or in
all of them. An implementation returns the whole bounded override map for a scope in one call
rather than answering per identifier, because the render path resolves hundreds of messages and
an override chain that becomes a lookup per message is a scale defect wearing a feature's name.

@since  2.0.0

### `organizationOverrides(string $site, string $organization, Kumwe\Localization\Domain\LocaleTag $locale)` → `array`

Read every override an organization within a site has recorded for one locale.

@param   string     $site          Site the organization belongs to.
@param   string     $organization  Organization identifier the overrides belong to.
@param   LocaleTag  $locale        Exact locale to read.

@return  array<string, string>  ICU patterns keyed by message identifier, empty when the
         organization has overridden nothing.

@since   2.0.0

### `siteOverrides(string $site, Kumwe\Localization\Domain\LocaleTag $locale)` → `array`

Read every override a site has recorded for one locale.

@param   string     $site    Site identifier the overrides belong to.
@param   LocaleTag  $locale  Exact locale to read.

@return  array<string, string>  ICU patterns keyed by message identifier, empty when the site
         has overridden nothing.

@since   2.0.0

## `Kumwe\Localization\Application\MessageOverrideStore`

Write face of the two administered override layers, kept apart from the read face the chain uses.

`MessageOverrideRepository` answers the render path and is shaped for it: whole scope, one call, no
bookkeeping. This is the other half — what an administration surface needs to list, write and
withdraw an override. They are separate ports because they have opposite pressures: the read side
is on the hot path and must stay a single bounded fetch, while the write side is rare, per
identifier, and needs to say when and by whom.

An implementation stores at most one pattern per layer, scope, locale and identifier, so writing
the same identifier twice replaces the wording rather than accumulating rows an operator would then
have to reconcile.

@since  2.0.0

### `lockSite(string $site)` → `void`

Serialize administered wording mutations for one site inside the caller's transaction.

The quota is scoped more narrowly than a site, but locking the site's durable identity gives even
an empty override scope a row all three supported engines can lock. Wording writes are rare, so the
deliberately coarse lock is preferable to a race-prone count or a second quota-ledger table.

@param   string  $site  Site whose wording mutation is about to count and write.

@return  void

@since   2.0.0

### `overrides(Kumwe\Localization\Domain\MessageCatalogueLayer $layer, string $site, ?string $organization (optional), ?Kumwe\Localization\Domain\LocaleTag $locale (optional))` → `array`

List every override stored for one scope, newest wording included.

@param   MessageCatalogueLayer  $layer         Administered layer to list, `Site` or `Organization`.
@param   string                 $site          Site the scope belongs to.
@param   ?string                $organization  Organization within that site, or null at site level.
@param   ?LocaleTag             $locale        Restrict to one locale, or null for every locale.

@return  list<MessageOverrideRecord>  Stored overrides ordered by locale and then identifier, so a
         screen renders them in a stable order across reads.

@since   2.0.0

### `put(Kumwe\Localization\Application\MessageOverrideRecord $override)` → `void`

Store or replace the wording one layer carries for one identifier at one locale.

@param   MessageOverrideRecord  $override  The override to write, replacing any pattern already
         stored for the same layer, scope, locale and identifier.

@return  void

@since   2.0.0

### `remove(Kumwe\Localization\Domain\MessageCatalogueLayer $layer, string $site, ?string $organization, Kumwe\Localization\Domain\LocaleTag $locale, string $identifier)` → `bool`

Withdraw one override so the layer below it answers again.

@param   MessageCatalogueLayer  $layer         Administered layer the override sits in.
@param   string                 $site          Site the scope belongs to.
@param   ?string                $organization  Organization within that site, or null at site level.
@param   LocaleTag              $locale        Locale the override applies to.
@param   string                 $identifier    Message identifier to stop overriding.

@return  bool  True when a row was removed, false when nothing was stored for that combination.

@since   2.0.0

## `Kumwe\Localization\Application\MessagePatternFormatter`

Port that substitutes a parameter bag into a resolved message pattern for one locale.

It is separated from the translator because the two answer different questions: the translator
decides *which* pattern applies, and this decides *what the pattern says* once the values are
known. Plural category, gender selection, ordinal form, number, currency and date rendering are
all this port's responsibility, and all of them are locale-dependent in ways string substitution
cannot express — the languages in scope span one plural category, two, three and six, so a
formatter that substitutes rather than selects would be wrong in Arabic on every count it renders.

@since  2.0.0

### `format(string $pattern, array $parameters, Kumwe\Localization\Domain\LocaleTag $locale)` → `string`

Format one pattern for one locale.

@param   string                                                   $pattern     ICU MessageFormat pattern.
@param   array<string, string|int|float|bool|\DateTimeInterface>  $parameters  Values the pattern names,
         keyed by placeholder name.
@param   LocaleTag                                                $locale      Locale whose plural rules,
         number symbols and date formats apply.

@return  string  The formatted message.

@throws  MessageFormattingFailed  When the pattern does not compile, or the parameters cannot
         satisfy it.

@since   2.0.0

## `Kumwe\Localization\Application\MessagePatternValidator`

Validates an ICU MessageFormat pattern before it reaches a durable catalogue layer.

Formatting is intentionally a request-time concern, but syntax validity is a write-time and build-time
invariant: one malformed override or compiled translation must be refused before every surface that
looks the identifier up begins throwing. The port keeps the application service independent of `intl`.

@since  2.0.0

### `validate(string $pattern, Kumwe\Localization\Domain\LocaleTag $locale)` → `void`

Refuse a pattern ICU cannot compile for its declared locale.

@param   string     $pattern  Candidate ICU MessageFormat pattern.
@param   LocaleTag  $locale   Locale whose grammar and plural rules the pattern targets.

@return  void

@throws  MessageFormattingFailed  When the pattern cannot be compiled for the locale.

@since   2.0.0

## `Kumwe\Localization\Application\SupportedLocales`

The locales this installation carries, in preference order, with `en-GB` as the source of record.

Version 2 states nine: `en-GB`, `en-US`, `af`, `de`, `he`, `ar`, `es`, `pt-BR` and `zh-Hans`.
They are held in one place because three separate inputs — an explicit choice, an
`Accept-Language` header and the site's `default_locale` setting — all have to be reduced to a
locale that actually exists, and doing that reduction differently in three places is how a site
ends up rendering a language it does not carry. Order is preference order, so a request for a
language without a region resolves to the first variant declared for it: `en` becomes `en-GB`,
not `en-US`.

The source locale is separate from the rest. Every message is authored in it, so it is the last
fallback before an identifier is returned as its own text.

@since  2.0.0

- `SOURCE`: The locale every message is authored in.  @var    string @since  2.0.0
- `VERSION_TWO`: The Version 2 language set, in preference order, with the source locale first. @var
non-empty-list<string> @since 2.0.0

### `__construct(array $tags (optional), string $source (optional))`

Build a registry from a declared list of tags and a declared source.

@param   list<string>  $tags    Locale tags this installation carries, in preference order.
@param   string        $source  Locale every message is authored in; must appear in $tags.

@throws  InvalidLocaleTag  When a tag is malformed, or the source is not among the tags.

@since   2.0.0

### `all()` → `array`

Every carried locale, in preference order.

@return  non-empty-list<LocaleTag>  The declared locales, normalised.

@since   2.0.0

### `best(Kumwe\Localization\Domain\LocaleTag $candidate)` → `?Kumwe\Localization\Domain\LocaleTag`

Reduce a candidate tag to the carried locale that best serves it.

An exact match wins. Failing that, the first carried locale sharing the candidate's language
subtag is used, which is what turns the shipped `default_locale` of `en` into `en-GB` without
anybody having to restate the setting. A candidate whose language is not carried at all
resolves to nothing, and the caller decides whether that is a refusal or a fall-through.

@param   LocaleTag  $candidate  Tag offered by a request, a header or a setting.

@return  ?LocaleTag  The carried locale to use, or null when the language is not carried.

@since   2.0.0

### `carries(Kumwe\Localization\Domain\LocaleTag $candidate)` → `bool`

Whether a tag names a carried locale exactly.

@param   LocaleTag  $candidate  Tag to test.

@return  bool  True when the exact tag is carried.

@since   2.0.0

### `source()` → `Kumwe\Localization\Domain\LocaleTag`

The locale every message is authored in, and the last fallback before the identifier itself.

@return  LocaleTag  The source locale.

@since   2.0.0

### `tags()` → `array`

Every carried locale as its canonical string form.

@return  non-empty-list<string>  Tags in preference order.

@since   2.0.0

## `Kumwe\Localization\Application\TranslationScope`

Which site, and optionally which organization within it, the administered override layers apply to.

The two upper steps of the override chain are scoped rather than global, so a resolver needs to
know whose wording it is resolving before it can read them. Holding the pair as one value keeps
an organization override from being read without the site it belongs to, which is what stops one
site's terminology from reaching another's.

@since  2.0.0

Public immutable data: `$organization` (`?string`), `$site` (`string`). Constructor/factory contracts below specify
each field.

### `__construct(string $site, ?string $organization (optional))`

Bind a scope to a site and, when there is one, an organization inside it.

@param  string   $site          Site identifier whose overrides apply.
@param  ?string  $organization  Organization identifier whose overrides apply, or null when the
        unit of work is not inside one.

@since  2.0.0

### `default()` → `Kumwe\Localization\Application\TranslationScope`

The scope of an installation that has no site context yet, such as a boot-time console command.

@return  self  Scope naming the default site and no organization.

@since   2.0.0

### `key()` → `string`

A stable key two scopes compare on, used to memoise a resolved chain per unit of work.

@return  string  Site identifier, then the organization identifier when there is one.

@since   2.0.0

## `Kumwe\Localization\Application\Translator`

Port every layer reads user-facing text through, given an identifier, a parameter bag and a locale.

This is the whole translation contract as a caller sees it. The locale is an argument rather than
process state, and that is the load-bearing decision: this platform runs long-lived queue workers
and a scheduler, so a job for a site in Arabic and the next job for a site in German are handled
by the same process, and a selector held in the process would leak the first job's language into
the second. Passing the locale means two units of work in one worker cannot contaminate each
other, and it is the reason the operating-system locale is never consulted.

An implementation resolves the identifier through the override chain, formats the result through
ICU MessageFormat, and never returns an empty string: a message the catalogues do not carry comes
back as its own identifier, because a visibly untranslated interface is recoverable and a silently
blank one is not.

@since  2.0.0

### `has(string $identifier, ?Kumwe\Localization\Domain\LocaleTag $locale (optional))` → `bool`

Whether any layer of the chain carries a message for an identifier at a locale.

Callers that must distinguish "translated to the identifier" from "genuinely translated" — an
extraction gate, a catalogue completeness check — ask this rather than comparing the returned
string against the identifier.

@param   string      $identifier  Stable message identifier.
@param   ?LocaleTag  $locale      Locale to test, or null for the locale in flight.

@return  bool  True when a pattern exists at this locale or one of its fallbacks.

@since   2.0.0

### `translate(string $identifier, array $parameters (optional), ?Kumwe\Localization\Domain\LocaleTag $locale (optional))` → `string`

Resolve and format one message.

@param   string                                                   $identifier  Stable message identifier.
@param   array<string, string|int|float|bool|\DateTimeInterface>  $parameters  Values the ICU pattern
         names, keyed by placeholder name.
@param   ?LocaleTag                                               $locale      Locale to render in, or
         null to use the locale resolved for the unit of work in flight.

@return  string  The formatted message, or the identifier itself when no layer carries it.

@throws  \Kumwe\Localization\Domain\InvalidMessageIdentifier  When the identifier does not
         satisfy the frozen grammar.
@throws  MessageFormattingFailed  When the resolved pattern is not valid ICU MessageFormat, or
         the supplied parameters cannot satisfy it.

@since   2.0.0

## `Kumwe\Localization\ConfigProvider`

Declares only operation-scoped runtime services; host ports and contexts must be supplied explicitly.

@since 0.1.0

### `__invoke()` → `array`

Return deterministic Mezzio container configuration without constructing any service.

@return array{dependencies: array{factories: array<class-string,
        class-string<CatalogueTranslatorFactory>|class-string<LocaleNegotiatorFactory>>,
        aliases: array<class-string, class-string>, shared: array<class-string, bool>},
        kumwe: array{localization: array{maximum_accept_language_bytes: int}}}

@since 0.1.0

## `Kumwe\Localization\Container\CatalogueTranslatorFactory`

Constructs a translator from explicit host ports and the context of one operation.

@since 0.1.0

### `__invoke(Psr\Container\ContainerInterface $container)` → `Kumwe\Localization\Application\CatalogueTranslator`

Resolve the five documented collaborators without retaining the container.

@param ContainerInterface $container Host container for this operation.

@return CatalogueTranslator A fresh translator with an initially empty catalogue cache.

@throws InvalidArgumentException When a service does not implement its declared contract.

@since 0.1.0

## `Kumwe\Localization\Container\LocaleNegotiatorFactory`

Constructs negotiation with a host default provider and explicitly bounded header parsing.

@since 0.1.0

### `__invoke(Psr\Container\ContainerInterface $container)` → `Kumwe\Localization\Application\LocaleNegotiator`

Resolve only localization services and the package's own options.

@param ContainerInterface $container Host container for the current host scope.

@return LocaleNegotiator A fresh, stateless negotiator.

@throws InvalidArgumentException When services or the positive integer header limit are invalid.

@since 0.1.0

## `Kumwe\Localization\Domain\InvalidLocaleTag`

Raised when a value offered as a locale is not a language tag this platform will resolve.

Locale tags arrive from three untrusted-ish places — a stored site setting, an `Accept-Language`
header and an explicit request parameter — and every one of them reaches a catalogue lookup and a
rendered `lang` attribute. Refusing a malformed tag by name here keeps a crafted value from
becoming a file path, a formatter argument or markup, and gives the operator a message that says
which tag was rejected rather than a generic argument error.

@since  2.0.0

### `malformed(string $candidate)` → `Kumwe\Localization\Domain\InvalidLocaleTag`

State that a candidate does not match the language-tag grammar.

@param   string  $candidate  Value that was offered as a locale tag.

@return  self  Exception naming the rejected candidate.

@since   2.0.0

### `unsupported(string $candidate, array $supported)` → `Kumwe\Localization\Domain\InvalidLocaleTag`

State that a well-formed tag names a locale this installation does not carry.

@param   string        $candidate  Well-formed tag that matched no supported locale.
@param   list<string>  $supported  Locales this installation does carry, in declaration order.

@return  self  Exception naming the rejected tag and what is available instead.

@since   2.0.0

## `Kumwe\Localization\Domain\InvalidMessageIdentifier`

Raised when a message identifier does not satisfy the grammar the translation contract froze.

The identifier is the one part of the translation contract that cannot be corrected later: once
eight languages carry a translation filed under it, renaming it discards that work. Each factory
below states which rule was broken, because the author of a new message needs to know whether the
identifier was shaped wrongly, was written as English prose, or claimed a namespace belonging to
someone else.

@since  2.0.0

### `malformed(string $identifier)` → `Kumwe\Localization\Domain\InvalidMessageIdentifier`

State that an identifier does not match the dotted lowercase grammar.

@param   string  $identifier  Value that was offered as a message identifier.

@return  self  Exception naming the rejected value and restating the grammar.

@since   2.0.0

### `outsideNamespace(string $identifier, string $namespace)` → `Kumwe\Localization\Domain\InvalidMessageIdentifier`

State that an identifier sits outside the namespace its contributor may claim.

@param   string  $identifier  Identifier the contributor tried to register.
@param   string  $namespace   Dotted namespace the contributor is entitled to.

@return  self  Exception naming both the identifier and the namespace it had to sit under.

@since   2.0.0

### `sourceText(string $identifier)` → `Kumwe\Localization\Domain\InvalidMessageIdentifier`

State that an identifier is the source text rather than a stable name for it.

@param   string  $identifier  Value that reads as prose rather than as an identifier.

@return  self  Exception naming the rejected value and why source text may never be a key.

@since   2.0.0

## `Kumwe\Localization\Domain\LocaleTag`

A normalised language tag, the direction it is written in, and the tags it falls back through.

Every locale that reaches a catalogue lookup, an ICU formatter or a rendered `lang` attribute
passes through this type first, so casing and separators are settled in one place: `pt_br`,
`PT-BR` and `pt-BR` are the same value, and a caller can compare two locales with `equals()`
rather than with a normalisation of its own. The fallback list is what lets a catalogue carry
`pt-BR` while a message that is identical across Portuguese variants is authored once under `pt`.

@since  2.0.0

Public immutable data: `$language` (`string`), `$region` (`?string`), `$script` (`?string`). Constructor/factory
contracts below specify each field.

### `direction()` → `Kumwe\Localization\Domain\TextDirection`

The direction this locale's script is laid out in.

The script subtag decides when one is present, so `az-Arab` is right-to-left while `az` is not;
otherwise the language subtag does. This is the value the layouts emit as `dir`.

@return  TextDirection  Right-to-left for the Arabic and Hebrew script families, otherwise left-to-right.

@since   2.0.0

### `equals(Kumwe\Localization\Domain\LocaleTag $other)` → `bool`

Whether two tags name the same locale after normalisation.

@param   self  $other  Tag to compare against.

@return  bool  True when language, script and region all match.

@since   2.0.0

### `fallbacks()` → `array`

The tags a lookup tries, most specific first, before it leaves this locale entirely.

@return  non-empty-list<string>  This tag, then the same tag with the region dropped, then the
         bare language subtag, without repeating a form the tag already had.

@since   2.0.0

### `fromString(string $tag)` → `Kumwe\Localization\Domain\LocaleTag`

Parse and normalise a language tag offered as a string.

Underscores are accepted as separators because a stored setting and an operating-system locale
both use them, and the site settings writer already normalises them the same way. Anything
beyond a language, an optional script and an optional region is refused rather than truncated,
so an extended tag never resolves to a locale the caller did not name.

@param   string  $tag  Candidate language tag, such as `en-GB`, `pt_br` or `zh-Hans`.

@return  self  The normalised tag.

@throws  InvalidLocaleTag  When the value is not a language subtag with optional script and region.

@since   2.0.0

### `toString()` → `string`

The canonical string form, which is what a `lang` attribute and a catalogue file name carry.

@return  string  Language, then script, then region, joined by hyphens.

@since   2.0.0

## `Kumwe\Localization\Domain\MessageCatalogue`

The messages one layer carries for one locale, as an immutable identifier-to-pattern map.

This is the runtime shape, not the authored one: patterns arrive already extracted from XLIFF by
the build, so a lookup is an array access against a structure the opcode cache already holds. It
carries no formatting behaviour and no fallback behaviour of its own — a catalogue answers only
"do I have this identifier, and what is its pattern" — because the order layers are consulted in
belongs to the resolver and the substitution belongs to the formatter.

@since  2.0.0

Public immutable data: `$layer` (`Kumwe\Localization\Domain\MessageCatalogueLayer`), `$locale`
(`Kumwe\Localization\Domain\LocaleTag`), `$messages` (`array`). Constructor/factory contracts below specify each
field.

### `__construct(Kumwe\Localization\Domain\LocaleTag $locale, Kumwe\Localization\Domain\MessageCatalogueLayer $layer, array $messages)`

Hold one layer's messages for one locale.

@param  LocaleTag              $locale    Locale these patterns are written in.
@param  MessageCatalogueLayer  $layer     Chain step this catalogue occupies.
@param  array<string, string>  $messages  ICU MessageFormat patterns, keyed by message identifier.

@since  2.0.0

### `count()` → `int`

How many messages this catalogue carries.

@return  int<0, max>  Count of filed identifiers.

@since   2.0.0

### `empty(Kumwe\Localization\Domain\LocaleTag $locale, Kumwe\Localization\Domain\MessageCatalogueLayer $layer)` → `Kumwe\Localization\Domain\MessageCatalogue`

An empty catalogue for a layer that carries nothing at this locale.

Absence is expressed as an empty catalogue rather than as null so that a resolver walks a
uniform chain instead of branching on which layers happen to exist for a given site.

@param   LocaleTag              $locale  Locale the empty catalogue stands in for.
@param   MessageCatalogueLayer  $layer   Chain step the empty catalogue occupies.

@return  self  A catalogue carrying no messages.

@since   2.0.0

### `has(string $identifier)` → `bool`

Whether this catalogue carries a pattern for an identifier.

@param   string  $identifier  Message identifier to test.

@return  bool  True when the identifier is present in this layer.

@since   2.0.0

### `identifiers()` → `array`

Every identifier this catalogue carries.

@return  list<string>  Identifiers in the order the catalogue stores them, which compilation
         fixed as ascending byte order.

@since   2.0.0

### `pattern(string $identifier)` → `?string`

Read the pattern filed under an identifier.

@param   string  $identifier  Message identifier to read.

@return  ?string  The ICU pattern, or null when this layer does not carry the identifier.

@since   2.0.0

## `Kumwe\Localization\Domain\MessageCatalogueChain`

The four layers of one locale, ordered so that the first layer carrying an identifier wins.

Resolution is per identifier, never per file, and that distinction is the whole point of the
chain: an operator who wants to change one word does not have to take ownership of a catalogue,
and an extension that overrides three of core's messages still inherits the other several
hundred. The layers are assembled once for a locale and a scope and then queried many times,
because a page resolves hundreds of messages and a chain rebuilt per message would turn a
feature into a scale defect.

@since  2.0.0

Public immutable data: `$layers` (`array`), `$locale` (`Kumwe\Localization\Domain\LocaleTag`). Constructor/factory
contracts below specify each field.

### `__construct(Kumwe\Localization\Domain\LocaleTag $locale, array $layers)`

Hold the layers of one locale in resolution order.

@param  LocaleTag                         $locale  Locale every layer in this chain is written in.
@param  non-empty-list<MessageCatalogue>  $layers  Catalogues, most specific first.

@since  2.0.0

### `resolve(string $identifier)` → `?string`

Read the pattern the most specific layer carrying this identifier holds.

@param   string  $identifier  Message identifier to resolve.

@return  ?string  The winning ICU pattern, or null when no layer of this locale carries it.

@since   2.0.0

### `winningLayer(string $identifier)` → `?Kumwe\Localization\Domain\MessageCatalogueLayer`

Which layer would answer for an identifier.

This exists for the tests and the administration surfaces that have to show an operator where
a word is coming from; resolution itself does not need it.

@param   string  $identifier  Message identifier to attribute.

@return  ?MessageCatalogueLayer  The winning layer, or null when no layer carries the identifier.

@since   2.0.0

## `Kumwe\Localization\Domain\MessageCatalogueLayer`

One step of the override chain a message identifier is resolved through.

The chain exists for two reasons that look like one. The obvious one is translation: core ships
the base wording and everything above it corrects or completes it. The strategic one is
terminology adaptation — a health vertical relabelling "Client" as "Patient", an education
vertical as "Learner", a hospitality vertical as "Guest" — in one language or in all of them,
without forking core and without an extension shipping a parallel string table. That is why the
chain has four steps rather than two, and why resolution is per identifier rather than per file:
an operator changes one word without taking ownership of a catalogue.

@since  2.0.0

- `Core`: The base catalogue the CMS itself ships, which every other layer refines.  @since  2.0.0
- `Extension`: Messages an installed extension ships, which may add to and override core's.  @since  2.0.0
- `Organization`: Wording changed for one organization within a site, which overrides every layer below it. @since
2.0.0
- `Site`: Wording an operator has changed for one site, which overrides core and extensions alike.  @since  2.0.0

### `mostSpecificFirst()` → `array`

The layers in resolution order, most specific first.

A resolver walks this list and returns the first layer that carries the identifier, which is
what makes the most specific override win without any layer being merged into another.

@return  non-empty-list<self>  Organization, then site, then extension, then core.

@since   2.0.0

## `Kumwe\Localization\Domain\MessageIdentifier`

The stable, namespaced name a translated message is looked up by, never the message's own text.

This is the frozen half of the translation contract. A catalogue in nine languages files every
translation under this value, so it has to survive an English wording change: if the identifier
were the source text, correcting a typographical error in English would orphan that message in
eight other languages and every translator would redo work for a change that altered no meaning.
The grammar is deliberately the one `ContributionOwner` already applies to every other contributed
identifier — `core.` for what the CMS ships, `vendor.name.` for what an extension ships — so an
extension author learns one namespacing rule rather than two.

@since  2.0.0

Public immutable data: `$value` (`string`). Constructor/factory contracts below specify each field.

- `MAXIMUM_LENGTH`: Longest identifier the catalogue will file, in bytes. The bound exists so that an identifier is
always usable as an array key, a log field and an XLIFF unit attribute without truncation anywhere in the pipeline.
@var int @since 2.0.0
- `MINIMUM_SEGMENTS`: Fewest dotted segments an identifier may carry. Three is the point at which an identifier
names an owner, an area and a message rather than just a word, which is what keeps two unrelated surfaces from
colliding on `core.save`. @var int @since 2.0.0

### `fromString(string $identifier)` → `Kumwe\Localization\Domain\MessageIdentifier`

Validate an identifier against the grammar alone.

Use this where the owner is not in question — reading a compiled catalogue, or checking a
reference found in a template. Where a contributor is claiming the identifier, use
`ownedBy()` so the namespace is proven too.

@param   string  $identifier  Candidate identifier, such as `core.administrator.settings.save_action`.

@return  self  The validated identifier.

@throws  InvalidMessageIdentifier  When the value reads as source text, or does not match the
         dotted lowercase grammar, or carries fewer than three segments.

@since   2.0.0

### `isValid(string $identifier)` → `bool`

Whether a candidate would be accepted, without raising when it would not.

This is what the extraction gate and the catalogue compiler use to report every offending
identifier in one pass instead of stopping at the first.

@param   string  $identifier  Candidate identifier.

@return  bool  True when `fromString()` would accept the value.

@since   2.0.0

### `ownedBy(string $identifier, string $namespace)` → `Kumwe\Localization\Domain\MessageIdentifier`

Validate an identifier and prove that its contributor may claim it.

@param   string  $identifier  Candidate identifier the contributor wants to register.
@param   string  $namespace   Dotted namespace the contributor owns: `core`, or `vendor.name`.

@return  self  The validated identifier, guaranteed to sit under `$namespace`.

@throws  InvalidMessageIdentifier  When the grammar is broken, or the identifier sits outside
         the contributor's namespace.

@since   2.0.0

### `root()` → `string`

The owner root the identifier claims, which is `core` or an extension's vendor segment.

@return  string  The first dotted segment.

@since   2.0.0

## `Kumwe\Localization\Domain\TextDirection`

Inline writing direction a locale's script is laid out in.

The value is what a layout emits as its `dir` attribute, and it is the only thing the stylesheets
need in order to mirror themselves: every inline-axis rule in `assets/` is written with logical
properties, so the browser derives start and end from this one declaration rather than from a
second right-to-left stylesheet. It is a property of the locale's script, not of the site, so two
sites in one installation can render in opposite directions in the same process.

@since  2.0.0

- `LeftToRight`: Inline text runs from the start of the line on the left toward the right.  @since  2.0.0
- `RightToLeft`: Inline text runs from the start of the line on the right toward the left.  @since  2.0.0

No separately declared public methods. Standard PHP enum/exception behavior applies.

## `Kumwe\Localization\Infrastructure\IntlExtensionMissing`

Raised at construction when the intl extension the message formatter needs is not loaded.

The failure is loud and immediate rather than a quiet fall-back to a substituting formatter,
because a substituting formatter is wrong rather than approximate: the languages in scope span
one plural category, two, three and six, so `sprintf`-shaped substitution would render Arabic
counts incorrectly on every page and nothing would report it. `ext-intl` is a declared, hard
requirement of this package, so an installation without it is misconfigured, and the message
says exactly that.

@since  2.0.0

### `forMessageFormatting()` → `Kumwe\Localization\Infrastructure\IntlExtensionMissing`

State that the extension is absent and what has to be done about it.

@return  self  Exception naming the missing extension and the consequence of running without it.

@since   2.0.0

## `Kumwe\Localization\Infrastructure\IntlMessagePatternFormatter`

Formats ICU MessageFormat patterns through the intl extension, one locale per call.

ICU is what makes the nine languages expressible in one pipeline. Plural category selection is
the arithmetic reason: `zh-Hans` has one category, the European set has two, `he` has three
because it distinguishes a dual, and `ar` has six because it distinguishes zero, one, two, few,
many and other. Ordinals, gender selection, number and currency symbols and date skeletons are
locale-dependent in the same way. Adding a language whose plural class is not yet represented —
the four-category Slavic class is the next step outward — is therefore a catalogue change and not
an engineering change.

The locale is a call argument, never process state. `setlocale()` is never used and the operating
system's locales are never consulted, so a worker draining a queue can format one job in Arabic
and the next in German with no possibility of the first leaking into the second.

@since  2.0.0

### `__construct()`

Refuse to exist without the extension every format call depends on.

@throws  IntlExtensionMissing  When `ext-intl` is not loaded.

@since   2.0.0

### `format(string $pattern, array $parameters, Kumwe\Localization\Domain\LocaleTag $locale)` → `string`

Format one pattern for one locale.

Boolean parameters are rendered as `1` and `0` by ICU, which is rarely what a message means,
so they are converted to the strings `true` and `false` before they reach the formatter. That
makes a boolean usable as a `select` argument, which is how a message that varies on a flag
is written.

@param   string                                                   $pattern     ICU MessageFormat pattern.
@param   array<string, string|int|float|bool|\DateTimeInterface>  $parameters  Values the pattern names,
         keyed by placeholder name.
@param   LocaleTag                                                $locale      Locale whose plural rules,
         number symbols and date formats apply.

@return  string  The formatted message.

@throws  MessageFormattingFailed  When the pattern does not compile for this locale, or the
         parameters cannot satisfy it.

@since   2.0.0

### `validate(string $pattern, Kumwe\Localization\Domain\LocaleTag $locale)` → `void`

Compile a pattern without formatting it, so missing runtime parameters are not mistaken for bad syntax.

@param   string     $pattern  Candidate ICU MessageFormat pattern.
@param   LocaleTag  $locale   Locale whose grammar and plural rules the pattern targets.

@return  void

@throws  MessageFormattingFailed  When ICU refuses the pattern.

@since   2.0.0

