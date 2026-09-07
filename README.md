# Kumwe Localization

Portable locale negotiation, layered message catalogues and ICU formatting, extracted from Kumwe App.
Canonical namespace: `Kumwe\Localization\*`. Requires PHP 8.5 and ext-intl. Apache-2.0.

The package owns translation behavior and contracts; the host supplies catalogues, override storage,
trusted scope and its default locale. Authorization, settings mutation, persistence and delivery remain
in the host. No Kumwe dependency or historical namespace alias is used.

Implementation and verification are in progress on this Phase 1 extraction branch. The final
MIGRATION-HANDOFF.md will contain the exact source/consumer/test inventory and separate adoption gates.
