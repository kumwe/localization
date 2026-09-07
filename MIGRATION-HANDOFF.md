---
schema: "kumwe-migration-handoff/v2"
artifact_kind: "framework_php"
migration_id: "KUMWE-MIG-2026-005"
change_set: "KUMWE-CS-2026-005"
state: "draft_pr_open"
source:
  app:
    repository: "https://github.com/kumwe/app"
    baseline_commit: "960ce8ec00cf724a7cae03e5ba09c4852c9ab54e"
    examined_paths:
      - "src/Localization"
      - "tests/Unit/Localization"
      - "tests/Integration/Localization"
      - "tests/Functional/Localization"
      - "src/Kernel/ContainerFactory.php"
      - "composer.lock"
      - "docs/architecture/capability-index.md"
      - "AGENTS.md"
      - "docs/architecture/governance/decisions.md"
    old_namespace_roots:
      - "Kumwe\\App\\Localization\\"
    capability_index_sha256: "8fb2a8680bed6ac1456183bc9e48fe040194923b6d1b3331f04cea28bd5a9b2f"
  semantic_inputs: []
  examined_dependencies:
    - "kumwe/conversion v0.1.2"
    - "kumwe/extension-sdk v0.2.4"
    - "kumwe/producer v0.2.0"
    - "psr/container: factory signatures only; no Kumwe dependency selected"
    - "ext-intl: existing ICU formatting implementation"
  active_related_pull_requests:
    - "https://github.com/kumwe/app/pull/135"
target:
  repository: "https://github.com/kumwe/localization"
  artifact_identity: "kumwe/localization"
  canonical_namespace_or_abi: "Kumwe\\Localization"
  branch: "fix/release-integrity-successor-20260907"
  pull_request: "https://github.com/kumwe/localization/pull/2"
ownership:
  responsibility: "Portable locale, catalogue, translation, formatting and negotiation behavior."
  non_responsibilities:
    - "Authorization, site settings, trusted scope and administered mutation workflows"
    - "Doctrine and persistence/transaction policy"
    - "File catalogue loading/compilation/cache policy, middleware, Twig and delivery"
  allowed_dependency_ceiling: []
  implementation_owner: "kumwe/localization"
  next_consumer: "kumwe/app"
  public_manifests:
    -
      path: "resources/public-api/v1.json"
      sha256: "645454cdf3893932af21fba20fc8a1b6652619ca1ef7b91f05a5f23a5b9c5bbc"
    -
      path: "resources/capabilities/v1.json"
      sha256: "e5ffa4984b444276f5135bb541026734e5a85354d112642bdb244d08972a8697"
    -
      path: "resources/service-map/v1.json"
      sha256: "c7e70a8b5e5ea7bfd70d19cccbb17f111357f7f0f61fcabc7bd14cf5437eca3e"
  intentionally_excluded:
    - "src/Localization/Application/SiteDefaultLocale.php"
    - "src/Localization/Application/MessageOverrideService.php"
    - "src/Localization/Infrastructure/DoctrineMessageOverrideRepository.php"
    - "src/Localization/Infrastructure/CompiledMessageCatalogueRepository.php"
    - "src/Localization/Infrastructure/MessageCatalogueCompiler.php"
    - "src/Localization/Infrastructure/XliffCatalogue.php"
    - "src/Localization/Infrastructure/XliffCatalogueReader.php"
    - "src/Localization/Infrastructure/ArrayMessageOverrideRepository.php"
    - "src/Localization/Http"
    - "src/Localization/Presentation"
framework_php:
  composer_package: "kumwe/localization"
  canonical_namespace: "Kumwe\\Localization"
  public_api_manifest: "resources/public-api/v1.json"
  capability_manifest: "resources/capabilities/v1.json"
  service_map: "resources/service-map/v1.json"
  extracted_symbols:
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\ActiveLocale"
      new_fqcn: "Kumwe\\Localization\\Application\\ActiveLocale"
      source_path: "src/Localization/Application/ActiveLocale.php"
      target_path: "src/Application/ActiveLocale.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "adoptLocale"
        - "adoptScope"
        - "begin"
        - "end"
        - "generation"
        - "locale"
        - "scope"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\CatalogueTranslator"
      new_fqcn: "Kumwe\\Localization\\Application\\CatalogueTranslator"
      source_path: "src/Localization/Application/CatalogueTranslator.php"
      target_path: "src/Application/CatalogueTranslator.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "attribution"
        - "has"
        - "translate"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\LocaleNegotiator"
      new_fqcn: "Kumwe\\Localization\\Application\\LocaleNegotiator"
      source_path: "src/Localization/Application/LocaleNegotiator.php"
      target_path: "src/Application/LocaleNegotiator.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "negotiate"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "Namespace moved; constructor SiteDefaultLocale becomes DefaultLocaleProvider (LOCALIZATION-001); executable behavior otherwise preserved."
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\MessageCatalogueRepository"
      new_fqcn: "Kumwe\\Localization\\Application\\MessageCatalogueRepository"
      source_path: "src/Localization/Application/MessageCatalogueRepository.php"
      target_path: "src/Application/MessageCatalogueRepository.php"
      kind: "interface"
      public_methods:
        - "catalogue"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\MessageFormattingFailed"
      new_fqcn: "Kumwe\\Localization\\Application\\MessageFormattingFailed"
      source_path: "src/Localization/Application/MessageFormattingFailed.php"
      target_path: "src/Application/MessageFormattingFailed.php"
      kind: "class"
      public_methods:
        - "pattern"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\MessageOverrideRecord"
      new_fqcn: "Kumwe\\Localization\\Application\\MessageOverrideRecord"
      source_path: "src/Localization/Application/MessageOverrideRecord.php"
      target_path: "src/Application/MessageOverrideRecord.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "toArray"
      public_properties:
        - "identifier"
        - "layer"
        - "locale"
        - "organization"
        - "pattern"
        - "site"
        - "updatedAt"
      public_constants: []
      exceptions: []
      serialization_contract: "toArray retains layer/site/organization/locale/identifier/pattern/updated_at with RFC3339 timezone offset."
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\MessageOverrideRepository"
      new_fqcn: "Kumwe\\Localization\\Application\\MessageOverrideRepository"
      source_path: "src/Localization/Application/MessageOverrideRepository.php"
      target_path: "src/Application/MessageOverrideRepository.php"
      kind: "interface"
      public_methods:
        - "organizationOverrides"
        - "siteOverrides"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\MessageOverrideStore"
      new_fqcn: "Kumwe\\Localization\\Application\\MessageOverrideStore"
      source_path: "src/Localization/Application/MessageOverrideStore.php"
      target_path: "src/Application/MessageOverrideStore.php"
      kind: "interface"
      public_methods:
        - "lockSite"
        - "overrides"
        - "put"
        - "remove"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\MessagePatternFormatter"
      new_fqcn: "Kumwe\\Localization\\Application\\MessagePatternFormatter"
      source_path: "src/Localization/Application/MessagePatternFormatter.php"
      target_path: "src/Application/MessagePatternFormatter.php"
      kind: "interface"
      public_methods:
        - "format"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\MessagePatternValidator"
      new_fqcn: "Kumwe\\Localization\\Application\\MessagePatternValidator"
      source_path: "src/Localization/Application/MessagePatternValidator.php"
      target_path: "src/Application/MessagePatternValidator.php"
      kind: "interface"
      public_methods:
        - "validate"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\SupportedLocales"
      new_fqcn: "Kumwe\\Localization\\Application\\SupportedLocales"
      source_path: "src/Localization/Application/SupportedLocales.php"
      target_path: "src/Application/SupportedLocales.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "all"
        - "best"
        - "carries"
        - "source"
        - "tags"
      public_properties: []
      public_constants:
        - "SOURCE"
        - "VERSION_TWO"
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\TranslationScope"
      new_fqcn: "Kumwe\\Localization\\Application\\TranslationScope"
      source_path: "src/Localization/Application/TranslationScope.php"
      target_path: "src/Application/TranslationScope.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "default"
        - "key"
      public_properties:
        - "organization"
        - "site"
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Application\\Translator"
      new_fqcn: "Kumwe\\Localization\\Application\\Translator"
      source_path: "src/Localization/Application/Translator.php"
      target_path: "src/Application/Translator.php"
      kind: "interface"
      public_methods:
        - "has"
        - "translate"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\InvalidLocaleTag"
      new_fqcn: "Kumwe\\Localization\\Domain\\InvalidLocaleTag"
      source_path: "src/Localization/Domain/InvalidLocaleTag.php"
      target_path: "src/Domain/InvalidLocaleTag.php"
      kind: "class"
      public_methods:
        - "malformed"
        - "unsupported"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\InvalidMessageIdentifier"
      new_fqcn: "Kumwe\\Localization\\Domain\\InvalidMessageIdentifier"
      source_path: "src/Localization/Domain/InvalidMessageIdentifier.php"
      target_path: "src/Domain/InvalidMessageIdentifier.php"
      kind: "class"
      public_methods:
        - "malformed"
        - "outsideNamespace"
        - "sourceText"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\LocaleTag"
      new_fqcn: "Kumwe\\Localization\\Domain\\LocaleTag"
      source_path: "src/Localization/Domain/LocaleTag.php"
      target_path: "src/Domain/LocaleTag.php"
      kind: "class"
      public_methods:
        - "direction"
        - "equals"
        - "fallbacks"
        - "fromString"
        - "toString"
      public_properties:
        - "language"
        - "region"
        - "script"
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\MessageCatalogue"
      new_fqcn: "Kumwe\\Localization\\Domain\\MessageCatalogue"
      source_path: "src/Localization/Domain/MessageCatalogue.php"
      target_path: "src/Domain/MessageCatalogue.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "count"
        - "empty"
        - "has"
        - "identifiers"
        - "pattern"
      public_properties:
        - "layer"
        - "locale"
        - "messages"
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\MessageCatalogueChain"
      new_fqcn: "Kumwe\\Localization\\Domain\\MessageCatalogueChain"
      source_path: "src/Localization/Domain/MessageCatalogueChain.php"
      target_path: "src/Domain/MessageCatalogueChain.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "resolve"
        - "winningLayer"
      public_properties:
        - "layers"
        - "locale"
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\MessageCatalogueLayer"
      new_fqcn: "Kumwe\\Localization\\Domain\\MessageCatalogueLayer"
      source_path: "src/Localization/Domain/MessageCatalogueLayer.php"
      target_path: "src/Domain/MessageCatalogueLayer.php"
      kind: "enum"
      public_methods:
        - "mostSpecificFirst"
      public_properties: []
      public_constants:
        - "Core"
        - "Extension"
        - "Organization"
        - "Site"
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\MessageIdentifier"
      new_fqcn: "Kumwe\\Localization\\Domain\\MessageIdentifier"
      source_path: "src/Localization/Domain/MessageIdentifier.php"
      target_path: "src/Domain/MessageIdentifier.php"
      kind: "class"
      public_methods:
        - "fromString"
        - "isValid"
        - "ownedBy"
        - "root"
      public_properties:
        - "value"
      public_constants:
        - "MAXIMUM_LENGTH"
        - "MINIMUM_SEGMENTS"
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Domain\\TextDirection"
      new_fqcn: "Kumwe\\Localization\\Domain\\TextDirection"
      source_path: "src/Localization/Domain/TextDirection.php"
      target_path: "src/Domain/TextDirection.php"
      kind: "enum"
      public_methods: []
      public_properties: []
      public_constants:
        - "LeftToRight"
        - "RightToLeft"
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Infrastructure\\IntlExtensionMissing"
      new_fqcn: "Kumwe\\Localization\\Infrastructure\\IntlExtensionMissing"
      source_path: "src/Localization/Infrastructure/IntlExtensionMissing.php"
      target_path: "src/Infrastructure/IntlExtensionMissing.php"
      kind: "class"
      public_methods:
        - "forMessageFormatting"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
    -
      old_fqcn: "Kumwe\\App\\Localization\\Infrastructure\\IntlMessagePatternFormatter"
      new_fqcn: "Kumwe\\Localization\\Infrastructure\\IntlMessagePatternFormatter"
      source_path: "src/Localization/Infrastructure/IntlMessagePatternFormatter.php"
      target_path: "src/Infrastructure/IntlMessagePatternFormatter.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "format"
        - "validate"
      public_properties: []
      public_constants: []
      exceptions: []
      serialization_contract: null
      compatibility: "preserved"
  consumers:
    app_code:
      - "src/Administrator/Automation/AutomationJobFormRegistry.php"
      - "src/Administrator/Http/Handler/AdministratorBusinessSecurityHandler.php"
      - "src/Administrator/Http/Handler/AdministratorLoginHandler.php"
      - "src/Administrator/Http/Handler/AdministratorMediaHandler.php"
      - "src/Administrator/Http/Handler/AdministratorStudioCompositionHandler.php"
      - "src/Administrator/Http/Handler/AdministratorWordingHandler.php"
      - "src/Administrator/Http/Middleware/AdministratorCsrfMiddleware.php"
      - "src/Administrator/Presentation/SitePresentationFormMapper.php"
      - "src/BusinessDefinition/Domain/EntityTypeDefinition.php"
      - "src/BusinessDefinition/Domain/FieldDefinition.php"
      - "src/BusinessDefinition/Domain/LocalizedDefinitionText.php"
      - "src/BusinessSchema/Delivery/Administrator/BusinessSchemaPlansHandler.php"
      - "src/BusinessSurface/Application/BusinessSurfaceCatalog.php"
      - "src/BusinessSurface/Application/BusinessSurfaceService.php"
      - "src/BusinessSurface/Delivery/Administrator/AdministratorBusinessSurfaceHandler.php"
      - "src/BusinessSurface/Delivery/Browser/BusinessCustomViewPresenter.php"
      - "src/BusinessSurface/Delivery/Browser/BusinessCustomViewRequest.php"
      - "src/BusinessSurface/Delivery/Browser/BusinessSchemaForm.php"
      - "src/BusinessSurface/Delivery/Browser/BusinessStructuredFieldForm.php"
      - "src/BusinessSurface/Delivery/Browser/GeneratedBusinessBrowserController.php"
      - "src/BusinessSurface/Delivery/Portal/PortalBusinessSurfaceHandler.php"
      - "src/Content/Application/ContentService.php"
      - "src/Content/Application/TranslationGroupRepository.php"
      - "src/Content/Domain/ContentEntry.php"
      - "src/Content/Domain/TranslationGroup.php"
      - "src/Content/Domain/TranslationGroupMember.php"
      - "src/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepository.php"
      - "src/Content/Presentation/TranslationGroupPresenter.php"
      - "src/Delivery/Console/StreamOutput.php"
      - "src/Extension/Contribution/TranslationGroupDeclaration.php"
      - "src/Http/Handler/HomePageHandler.php"
      - "src/Http/Handler/PublishedContentHandler.php"
      - "src/Kernel/ContainerFactory.php"
      - "src/Localization/Application/MessageOverrideService.php"
      - "src/Localization/Application/SiteDefaultLocale.php"
      - "src/Localization/Http/Middleware/LocaleNegotiationMiddleware.php"
      - "src/Localization/Http/Middleware/TranslationScopeMiddleware.php"
      - "src/Localization/Infrastructure/ArrayMessageOverrideRepository.php"
      - "src/Localization/Infrastructure/CompiledMessageCatalogueRepository.php"
      - "src/Localization/Infrastructure/DoctrineMessageOverrideRepository.php"
      - "src/Localization/Infrastructure/MessageCatalogueCompiler.php"
      - "src/Localization/Presentation/TranslationTwigExtension.php"
      - "src/Portal/Http/Handler/PortalApprovalHandler.php"
      - "src/Portal/Http/Handler/PortalLoginHandler.php"
      - "src/Portal/Http/Handler/PortalSecurityHandler.php"
      - "src/Portal/Http/Middleware/PortalCsrfMiddleware.php"
      - "src/Studio/Application/Host/StudioLocalizationHostPort.php"
    configuration_and_di:
      - "src/Kernel/ContainerFactory.php"
    reflection_and_string_references:
      - "docs/architecture/governance/core-growth-baseline.json"
      - "docs/interface-translation.md"
    fixtures_and_examples:
      - "tests/Integration/BusinessSurface/GeneratedBusinessBrowserIntegrationTest.php"
      - "tests/Integration/Content/MultilingualContentIntegrationTest.php"
      - "tests/Integration/Extension/ContributedContentTranslationIntegrationTest.php"
      - "tests/Integration/Localization/MessageOverrideIntegrationTest.php"
      - "tests/Support/InterfaceTranslation.php"
      - "tests/Support/TranslatesConsoleOutput.php"
      - "tests/Unit/Administrator/Http/Handler/AdministratorStudioHostHandlerTest.php"
      - "tests/Unit/Administrator/Http/Middleware/AdministratorAuthorizationMiddlewareTest.php"
      - "tests/Unit/BusinessDefinition/Domain/LocalizedDefinitionLabelTest.php"
      - "tests/Unit/BusinessSurface/Application/BusinessSurfaceCatalogTest.php"
      - "tests/Unit/Content/Application/ContentTranslationServiceTest.php"
      - "tests/Unit/Content/Application/ContributedContentTranslationTest.php"
      - "tests/Unit/Content/Application/ExtensionContentTranslationTest.php"
      - "tests/Unit/Content/Domain/ContentEntryTest.php"
      - "tests/Unit/Content/Domain/TranslationGroupTest.php"
      - "tests/Unit/Content/Infrastructure/Persistence/DoctrineContentRepositoryTest.php"
      - "tests/Unit/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepositoryTest.php"
      - "tests/Unit/Content/Presentation/TranslationGroupPresenterTest.php"
      - "tests/Unit/Delivery/Console/CommandDescriptionTest.php"
      - "tests/Unit/Http/Handler/HomePageHandlerTest.php"
      - "tests/Unit/Http/Handler/PublishedContentHandlerTest.php"
      - "tests/Unit/Localization/Application/CatalogueTranslatorTest.php"
      - "tests/Unit/Localization/Application/LocaleNegotiationTest.php"
      - "tests/Unit/Localization/Application/MessageOverrideServiceTest.php"
      - "tests/Unit/Localization/Domain/LocaleTagTest.php"
      - "tests/Unit/Localization/Domain/MessageIdentifierTest.php"
      - "tests/Unit/Localization/Http/LocaleNegotiationMiddlewareTest.php"
      - "tests/Unit/Localization/Http/TranslationScopeMiddlewareTest.php"
      - "tests/Unit/Localization/Infrastructure/IntlMessagePatternFormatterTest.php"
      - "tests/Unit/Localization/Infrastructure/MessageCatalogueCompilerTest.php"
      - "tests/Unit/Site/Application/PublicPageLocatorTest.php"
      - "tests/Unit/Studio/Application/Host/StudioLocalizationTelemetryHostPortTest.php"
      - "tests/Unit/Studio/Application/Projection/StudioContentProjectionServiceTest.php"
    external: []
  dependency_injection:
    mode: "config-provider"
    provider: "Kumwe\\Localization\\ConfigProvider"
    factories:
      - "Kumwe\\Localization\\Container\\CatalogueTranslatorFactory"
      - "Kumwe\\Localization\\Container\\LocaleNegotiatorFactory"
    aliases:
      - "Kumwe\\Localization\\Application\\Translator -> Kumwe\\Localization\\Application\\CatalogueTranslator"
    service_lifetimes:
      - "CatalogueTranslator and LocaleNegotiator: non-shared"
      - "ActiveLocale: explicitly host-supplied per operation; never global across concurrent requests"
      - "SupportedLocales and stateless formatter: direct construction, immutable/stateless sharing allowed"
      - "DefaultLocaleProvider: explicit host scope"
    configuration_keys:
      - "kumwe.localization.maximum_accept_language_bytes"
    provider_absence_reason: null
native_cpp: null
php_extension: null
tests:
  moved_or_added:
    - "tests/Support/ArrayMessageOverrideRepository.php"
    - "tests/Unit/ContainerTest.php"
    - "tests/Unit/Application/CatalogueTranslatorTest.php"
    - "tests/Unit/Application/LocaleNegotiatorTest.php"
    - "tests/Unit/Application/RecordAndCatalogueTest.php"
    - "tests/Unit/Domain/MessageIdentifierTest.php"
    - "tests/Unit/Domain/LocaleTagTest.php"
    - "tests/Unit/Infrastructure/IntlMessagePatternFormatterTest.php"
  remain_in_app_or_consumer:
    - "tests/Functional/Localization/ConsoleWordingTest.php"
    - "tests/Functional/Localization/ResolvedLocaleRenderingTest.php"
    - "tests/Integration/Localization/AdministratorWordingValidationIntegrationTest.php"
    - "tests/Integration/Localization/MessageOverrideIntegrationTest.php"
    - "tests/Unit/Localization/Application/LocaleNegotiationTest.php"
    - "tests/Unit/Localization/Application/MessageOverrideServiceTest.php"
    - "tests/Unit/Localization/Http/LocaleNegotiationMiddlewareTest.php"
    - "tests/Unit/Localization/Http/TranslationScopeMiddlewareTest.php"
    - "tests/Unit/Localization/Infrastructure/MessageCatalogueCompilerTest.php"
    - "tests/Unit/Localization/Presentation/TranslationTwigExtensionTest.php"
  split_tests:
    - "tests/Unit/Localization/Application/LocaleNegotiationTest.php: package takes negotiation/registry cases; App keeps SiteDefaultLocale settings reads, invalid settings, failing-store degradation and process caching cases."
    - "ArrayMessageOverrideRepository copied only to tests/Support with changed namespace; do not ship fixture as a public implementation."
  prohibited_duplicates:
    - "tests/Unit/Localization/Domain/LocaleTagTest.php"
    - "tests/Unit/Localization/Domain/MessageIdentifierTest.php"
    - "tests/Unit/Localization/Application/CatalogueTranslatorTest.php"
    - "tests/Unit/Localization/Infrastructure/IntlMessagePatternFormatterTest.php"
  corpora:
    - "resources/extraction/v1.json: original-source and executable-token SHA256 baseline for 23 moved types"
    - "Moved locale/identifier/translation/ICU unit vectors; independent negotiation/record/provider regression matrices"
documentation:
  charter: "CHARTER.md"
  readme: "README.md"
  public_api: "docs/public-api.md"
  architecture: "docs/architecture.md"
  integration_or_consumer: "docs/integration.md"
  examples:
    - "examples/translate.php"
    - "examples/container.php"
  changelog_record: "CHANGELOG.md: ## 0.1.1 (NRM-2026-006)"
release_expectations:
  version_policy: "SemVer; 0.1.1 successor under D-GOV-6; fresh verification required after publication."
  expected_artifact_types:
    - "Composer source archive"
    - "immutable GitHub tag/release after human merge"
  required_checks:
    - "Protected main before publication; exact stable published release reports immutable true."
    - "composer check"
    - "PHP8.5 ext-intl/zip full lane"
    - "no-dev built-ZIP dependency consumer with authoritative autoloader and actual ServiceManager"
    - "independent tag/archive/source/API/registry/provenance verification"
  required_registry_or_installer: "Composer/Packagist; first registry submission is maintainer-owned"
  required_external_attestation: true
next_task:
  phase_name: "Localization Phase 2 App adoption"
  permitted_only_when:
    - "The release-integrity successor is published from protected main and independently verified."
    - "Package PR human-merged"
    - "Immutable release independently verified by a fresh session"
    - "External RELEASE-ATTESTATION.yaml identifies exact source/archive/manifests and successful consumer install"
    - "No unported drift in extracted App symbols/tests since baseline"
  consumer_repository: "https://github.com/kumwe/app"
  dependency_or_native_change: "Require the exact independently verified kumwe/localization release; regenerate composer.lock through Composer."
  namespace_or_api_replacements:
    - "Kumwe\\App\\Localization\\Application\\ActiveLocale -> Kumwe\\Localization\\Application\\ActiveLocale"
    - "Kumwe\\App\\Localization\\Application\\CatalogueTranslator -> Kumwe\\Localization\\Application\\CatalogueTranslator"
    - "Kumwe\\App\\Localization\\Application\\LocaleNegotiator -> Kumwe\\Localization\\Application\\LocaleNegotiator"
    - "Kumwe\\App\\Localization\\Application\\MessageCatalogueRepository -> Kumwe\\Localization\\Application\\MessageCatalogueRepository"
    - "Kumwe\\App\\Localization\\Application\\MessageFormattingFailed -> Kumwe\\Localization\\Application\\MessageFormattingFailed"
    - "Kumwe\\App\\Localization\\Application\\MessageOverrideRecord -> Kumwe\\Localization\\Application\\MessageOverrideRecord"
    - "Kumwe\\App\\Localization\\Application\\MessageOverrideRepository -> Kumwe\\Localization\\Application\\MessageOverrideRepository"
    - "Kumwe\\App\\Localization\\Application\\MessageOverrideStore -> Kumwe\\Localization\\Application\\MessageOverrideStore"
    - "Kumwe\\App\\Localization\\Application\\MessagePatternFormatter -> Kumwe\\Localization\\Application\\MessagePatternFormatter"
    - "Kumwe\\App\\Localization\\Application\\MessagePatternValidator -> Kumwe\\Localization\\Application\\MessagePatternValidator"
    - "Kumwe\\App\\Localization\\Application\\SupportedLocales -> Kumwe\\Localization\\Application\\SupportedLocales"
    - "Kumwe\\App\\Localization\\Application\\TranslationScope -> Kumwe\\Localization\\Application\\TranslationScope"
    - "Kumwe\\App\\Localization\\Application\\Translator -> Kumwe\\Localization\\Application\\Translator"
    - "Kumwe\\App\\Localization\\Domain\\InvalidLocaleTag -> Kumwe\\Localization\\Domain\\InvalidLocaleTag"
    - "Kumwe\\App\\Localization\\Domain\\InvalidMessageIdentifier -> Kumwe\\Localization\\Domain\\InvalidMessageIdentifier"
    - "Kumwe\\App\\Localization\\Domain\\LocaleTag -> Kumwe\\Localization\\Domain\\LocaleTag"
    - "Kumwe\\App\\Localization\\Domain\\MessageCatalogue -> Kumwe\\Localization\\Domain\\MessageCatalogue"
    - "Kumwe\\App\\Localization\\Domain\\MessageCatalogueChain -> Kumwe\\Localization\\Domain\\MessageCatalogueChain"
    - "Kumwe\\App\\Localization\\Domain\\MessageCatalogueLayer -> Kumwe\\Localization\\Domain\\MessageCatalogueLayer"
    - "Kumwe\\App\\Localization\\Domain\\MessageIdentifier -> Kumwe\\Localization\\Domain\\MessageIdentifier"
    - "Kumwe\\App\\Localization\\Domain\\TextDirection -> Kumwe\\Localization\\Domain\\TextDirection"
    - "Kumwe\\App\\Localization\\Infrastructure\\IntlExtensionMissing -> Kumwe\\Localization\\Infrastructure\\IntlExtensionMissing"
    - "Kumwe\\App\\Localization\\Infrastructure\\IntlMessagePatternFormatter -> Kumwe\\Localization\\Infrastructure\\IntlMessagePatternFormatter"
    - "Retained SiteDefaultLocale implements Kumwe\\Localization\\Application\\DefaultLocaleProvider; preserve its current settings/failure/cache behavior."
  files_to_update:
    - "docs/architecture/governance/core-growth-baseline.json"
    - "docs/interface-translation.md"
    - "src/Administrator/Automation/AutomationJobFormRegistry.php"
    - "src/Administrator/Http/Handler/AdministratorBusinessSecurityHandler.php"
    - "src/Administrator/Http/Handler/AdministratorLoginHandler.php"
    - "src/Administrator/Http/Handler/AdministratorMediaHandler.php"
    - "src/Administrator/Http/Handler/AdministratorStudioCompositionHandler.php"
    - "src/Administrator/Http/Handler/AdministratorWordingHandler.php"
    - "src/Administrator/Http/Middleware/AdministratorCsrfMiddleware.php"
    - "src/Administrator/Presentation/SitePresentationFormMapper.php"
    - "src/BusinessDefinition/Domain/EntityTypeDefinition.php"
    - "src/BusinessDefinition/Domain/FieldDefinition.php"
    - "src/BusinessDefinition/Domain/LocalizedDefinitionText.php"
    - "src/BusinessSchema/Delivery/Administrator/BusinessSchemaPlansHandler.php"
    - "src/BusinessSurface/Application/BusinessSurfaceCatalog.php"
    - "src/BusinessSurface/Application/BusinessSurfaceService.php"
    - "src/BusinessSurface/Delivery/Administrator/AdministratorBusinessSurfaceHandler.php"
    - "src/BusinessSurface/Delivery/Browser/BusinessCustomViewPresenter.php"
    - "src/BusinessSurface/Delivery/Browser/BusinessCustomViewRequest.php"
    - "src/BusinessSurface/Delivery/Browser/BusinessSchemaForm.php"
    - "src/BusinessSurface/Delivery/Browser/BusinessStructuredFieldForm.php"
    - "src/BusinessSurface/Delivery/Browser/GeneratedBusinessBrowserController.php"
    - "src/BusinessSurface/Delivery/Portal/PortalBusinessSurfaceHandler.php"
    - "src/Content/Application/ContentService.php"
    - "src/Content/Application/TranslationGroupRepository.php"
    - "src/Content/Domain/ContentEntry.php"
    - "src/Content/Domain/TranslationGroup.php"
    - "src/Content/Domain/TranslationGroupMember.php"
    - "src/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepository.php"
    - "src/Content/Presentation/TranslationGroupPresenter.php"
    - "src/Delivery/Console/StreamOutput.php"
    - "src/Extension/Contribution/TranslationGroupDeclaration.php"
    - "src/Http/Handler/HomePageHandler.php"
    - "src/Http/Handler/PublishedContentHandler.php"
    - "src/Kernel/ContainerFactory.php"
    - "src/Localization/Application/MessageOverrideService.php"
    - "src/Localization/Application/SiteDefaultLocale.php"
    - "src/Localization/Http/Middleware/LocaleNegotiationMiddleware.php"
    - "src/Localization/Http/Middleware/TranslationScopeMiddleware.php"
    - "src/Localization/Infrastructure/ArrayMessageOverrideRepository.php"
    - "src/Localization/Infrastructure/CompiledMessageCatalogueRepository.php"
    - "src/Localization/Infrastructure/DoctrineMessageOverrideRepository.php"
    - "src/Localization/Infrastructure/MessageCatalogueCompiler.php"
    - "src/Localization/Presentation/TranslationTwigExtension.php"
    - "src/Portal/Http/Handler/PortalApprovalHandler.php"
    - "src/Portal/Http/Handler/PortalLoginHandler.php"
    - "src/Portal/Http/Handler/PortalSecurityHandler.php"
    - "src/Portal/Http/Middleware/PortalCsrfMiddleware.php"
    - "src/Studio/Application/Host/StudioLocalizationHostPort.php"
    - "tests/Integration/BusinessSurface/GeneratedBusinessBrowserIntegrationTest.php"
    - "tests/Integration/Content/MultilingualContentIntegrationTest.php"
    - "tests/Integration/Extension/ContributedContentTranslationIntegrationTest.php"
    - "tests/Integration/Localization/MessageOverrideIntegrationTest.php"
    - "tests/Support/InterfaceTranslation.php"
    - "tests/Support/TranslatesConsoleOutput.php"
    - "tests/Unit/Administrator/Http/Handler/AdministratorStudioHostHandlerTest.php"
    - "tests/Unit/Administrator/Http/Middleware/AdministratorAuthorizationMiddlewareTest.php"
    - "tests/Unit/BusinessDefinition/Domain/LocalizedDefinitionLabelTest.php"
    - "tests/Unit/BusinessSurface/Application/BusinessSurfaceCatalogTest.php"
    - "tests/Unit/Content/Application/ContentTranslationServiceTest.php"
    - "tests/Unit/Content/Application/ContributedContentTranslationTest.php"
    - "tests/Unit/Content/Application/ExtensionContentTranslationTest.php"
    - "tests/Unit/Content/Domain/ContentEntryTest.php"
    - "tests/Unit/Content/Domain/TranslationGroupTest.php"
    - "tests/Unit/Content/Infrastructure/Persistence/DoctrineContentRepositoryTest.php"
    - "tests/Unit/Content/Infrastructure/Persistence/DoctrineTranslationGroupRepositoryTest.php"
    - "tests/Unit/Content/Presentation/TranslationGroupPresenterTest.php"
    - "tests/Unit/Delivery/Console/CommandDescriptionTest.php"
    - "tests/Unit/Http/Handler/HomePageHandlerTest.php"
    - "tests/Unit/Http/Handler/PublishedContentHandlerTest.php"
    - "tests/Unit/Localization/Application/LocaleNegotiationTest.php"
    - "tests/Unit/Localization/Application/MessageOverrideServiceTest.php"
    - "tests/Unit/Localization/Http/LocaleNegotiationMiddlewareTest.php"
    - "tests/Unit/Localization/Http/TranslationScopeMiddlewareTest.php"
    - "tests/Unit/Localization/Infrastructure/MessageCatalogueCompilerTest.php"
    - "tests/Unit/Site/Application/PublicPageLocatorTest.php"
    - "tests/Unit/Studio/Application/Host/StudioLocalizationTelemetryHostPortTest.php"
    - "tests/Unit/Studio/Application/Projection/StudioContentProjectionServiceTest.php"
    - "composer.json"
    - "composer.lock"
    - "docs/architecture/capability-index.md"
  files_to_remove:
    - "src/Localization/Application/ActiveLocale.php"
    - "src/Localization/Application/CatalogueTranslator.php"
    - "src/Localization/Application/LocaleNegotiator.php"
    - "src/Localization/Application/MessageCatalogueRepository.php"
    - "src/Localization/Application/MessageFormattingFailed.php"
    - "src/Localization/Application/MessageOverrideRecord.php"
    - "src/Localization/Application/MessageOverrideRepository.php"
    - "src/Localization/Application/MessageOverrideStore.php"
    - "src/Localization/Application/MessagePatternFormatter.php"
    - "src/Localization/Application/MessagePatternValidator.php"
    - "src/Localization/Application/SupportedLocales.php"
    - "src/Localization/Application/TranslationScope.php"
    - "src/Localization/Application/Translator.php"
    - "src/Localization/Domain/InvalidLocaleTag.php"
    - "src/Localization/Domain/InvalidMessageIdentifier.php"
    - "src/Localization/Domain/LocaleTag.php"
    - "src/Localization/Domain/MessageCatalogue.php"
    - "src/Localization/Domain/MessageCatalogueChain.php"
    - "src/Localization/Domain/MessageCatalogueLayer.php"
    - "src/Localization/Domain/MessageIdentifier.php"
    - "src/Localization/Domain/TextDirection.php"
    - "src/Localization/Infrastructure/IntlExtensionMissing.php"
    - "src/Localization/Infrastructure/IntlMessagePatternFormatter.php"
  tests_to_remove:
    - "tests/Unit/Localization/Domain/LocaleTagTest.php"
    - "tests/Unit/Localization/Domain/MessageIdentifierTest.php"
    - "tests/Unit/Localization/Application/CatalogueTranslatorTest.php"
    - "tests/Unit/Localization/Infrastructure/IntlMessagePatternFormatterTest.php"
  tests_to_retain_or_add:
    - "tests/Functional/Localization/ConsoleWordingTest.php"
    - "tests/Functional/Localization/ResolvedLocaleRenderingTest.php"
    - "tests/Integration/Localization/AdministratorWordingValidationIntegrationTest.php"
    - "tests/Integration/Localization/MessageOverrideIntegrationTest.php"
    - "tests/Unit/Localization/Application/LocaleNegotiationTest.php"
    - "tests/Unit/Localization/Application/MessageOverrideServiceTest.php"
    - "tests/Unit/Localization/Http/LocaleNegotiationMiddlewareTest.php"
    - "tests/Unit/Localization/Http/TranslationScopeMiddlewareTest.php"
    - "tests/Unit/Localization/Infrastructure/MessageCatalogueCompilerTest.php"
    - "tests/Unit/Localization/Presentation/TranslationTwigExtensionTest.php"
    - "Host adapter parity for SiteDefaultLocale implementing DefaultLocaleProvider"
    - "ContainerFactory provider/port wiring, operation context isolation and translation integration"
  di_or_provisioning_changes:
    - "Register ConfigProvider explicitly; use only canonical interface aliases."
    - "Bind retained SiteDefaultLocale under DefaultLocaleProvider."
    - "Supply an operation-owned ActiveLocale and matching catalogue/override/formatter ports; remove superseded old-FQCN services."
  capability_index_changes:
    - "Generate capability index from exact locked release and committed manifests."
  changelog_and_evidence_changes:
    - "Record NRM-2026-006 and PR URL in App changelog."
    - "Write KUMWE-CS-2026-005 central record, KUMWE-MIG-2026-005 migration ledger and integration train; include unchanged external attestation."
    - "Do not claim a functional roadmap objective complete."
  verification_commands:
    - "bash tools/agent-setup.sh"
    - "composer qa"
    - "composer kumwe:capability-index"
    - "composer kumwe:core-growth-check"
    - "composer translation:check"
    - "composer translation:strings"
    - "Run retained Localization unit/integration/functional tests and affected database/browser delivery lanes"
concurrency:
  likely_conflict_files:
    - "composer.json"
    - "composer.lock"
    - "src/Kernel/ContainerFactory.php"
    - "docs/architecture/capability-index.md"
    - "CHANGELOG.md"
  related_migrations: []
  ownership_conflicts:
    - "MessageOverrideService/SiteDefaultLocale are retained hosts; never move their authorization or settings policy into the package."
  integration_train: null
  resolution_rule: "semantic-preservation"
governance:
  roadmap_source_sha256: "a202155ef1a65f5ab293d4f8397ebf4ac430db7f1e877c776bbe7851e6fe18d8"
  roadmap_refs: []
  non_roadmap_refs:
    - "NRM-2026-006"
  completion_claim: false
decisions:
  - "LOCALIZATION-001: replace LocaleNegotiator concrete host-settings dependency with DefaultLocaleProvider; host implements it in Phase2."
  - "LOCALIZATION-002: planned21 portable types plus2 ICU types,1 narrow port and3 DI types yields27 exported types; compiler/cache/authorization remain host-owned."
  - "LOCALIZATION-003: preserve executable tokens except namespace/interface inversion; do not claim stronger timezone, scope-key, quality-value or constructor validation semantics than App provides."
blockers:
  - "Publication and independent release verification have not occurred; App adoption remains blocked."

---

## Migration/implementation summary

All 21 planned portable types, two ICU types, one default-locale port and three explicit DI types are complete. This
Phase 1 changes only localization; no App source or test is removed here. The source maps and consumer inventories
above are authoritative.

## Public API and responsibility

Read docs/public-api.md and the three checked manifests. Interfaces, records and native PHP enums remain portable;
only factories depend on PSR-11. The preserved execution-token corpus independently checks 23 copied implementations
against the captured source bytes without needing App at test time.

## Capability reuse/semantic input review

The locked Kumwe dependency list and generated capability index were examined for locale/translation/catalogue
ownership. No installed package claims this closure; no Kumwe semantic dependency is selected. The source already
uses ext-intl, and PSR-11 is required only for explicit factories.

## Consumer inventory

The source scan above covers canonical imports and escaped FQCN strings across tracked source, tests, tools,
documentation and configuration. Repeat it at adoption, including bare imported short names and host container
entries, before removing the inventoried symbols.

## Test ownership

Move the four listed class-test files out of App on adoption; split LocaleNegotiationTest so host
settings-failure/caching behavior remains. All persistence, middleware, Twig, compiled catalogue, integration and
functional tests remain App responsibility. Do not move the test-only array fixture into the runtime archive.

## Next-task execution notes

Phase2 targets App master. Start with the exact verified released artifact and external attestation. Implement
DefaultLocaleProvider on retained SiteDefaultLocale before constructing the new negotiator. Preserve all host
settings and degradation logic. Update only inventoried extracted namespace references, register explicit DI ports,
regenerate Composer and live governance indexes, then remove obsolete class tests and definitions together.

## Drift check

Diff every source_path and moved/split test against App 960ce8ec00cf724a7cae03e5ba09c4852c9ab54e. Ignore unrelated
App movement under D-GOV-7. Any newer portable behavior requires a separate upstream successor PR/release before
adopting; no copying back into App or aliases.

## Validation recipe and observed local results

Local PHP 8.5.10: PHPUnit passes 38 tests and 350 assertions; PHPStan max/strict passes; namespace/dependency
architecture, complete 27-symbol manifests, 23-type executable extraction parity and a 46-file built-ZIP dependency
consumer with real ServiceManager pass. The actual App PackageManifests reader accepts the handoff as v2-manifested.
Local security advisory retrieval is blocked by network access; the unchanged fail-closed composer audit gate runs
in CI. Final-head CI supplies that result; no release attestation is invented or embedded here.

## Release-integrity successor 0.1.1

This PR follows the published 0.1.0 extraction and changes release automation and version-bound metadata only.
The existing migration/change-set and NRM identifiers continue to name the same extraction. Runtime source,
public method contracts, source baseline, consumer maps and the moved/retained test inventory are unchanged.

Before merging, the maintainer protects main and enables GitHub immutable releases. The workflow refuses an
unprotected release ref before tag/release mutation and verifies exact published immutable release metadata.
The setting applies only to future releases. Keep 0.1.0 and its tag intact; never move, delete or replace them.
A fresh independent verifier must attest the successor before dependent publication or App adoption.
See docs/releasing.md for the setup and verification order.
