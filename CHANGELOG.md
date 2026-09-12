# Changelog

## Unreleased

### Changed

- Add live package/CI/PHP/license badges and published installation guidance.
- Replace completed handover prose with current Core composition and a strict production release record.
- Preserve ICU, operation lifetime, host test retention and executable source-parity contracts.

## 0.1.1

- Unify PR and post-rebase release gates, dynamic release identity, tested publication retries,
  and administrator setup across the package family. Preserve immutable release and dependency evidence requirements.

- NRM-2026-006: require protected main before publication and verify the exact release is published,
  stable and immutable. Preserve existing tag ancestry and changelog checks; refuse mutable release metadata.
- Record a successor to 0.1.0 with refreshed release manifests and handoff. Runtime code, public signatures
  and the package/App test ownership split are unchanged. Maintainers enable protections before merging.

## 0.1.0

- Extract the portable locale, catalogue, translation, negotiation, override record and storage-port
  closure from Kumwe App, preserving canonical output and error behavior.
- Separate locale negotiation from host settings through DefaultLocaleProvider.
- Provide explicit translator/negotiator factories, manifests, package tests and release gates.
- Retain authorization, settings, persistence, catalogue build/cache adapters and delivery in App.
- NRM-2026-006: enabling extraction only; App adoption requires a separately verified release.
