# Changelog

## 0.1.0

- Extract the portable locale, catalogue, translation, negotiation, override record and storage-port
  closure from Kumwe App, preserving canonical output and error behavior.
- Separate locale negotiation from host settings through DefaultLocaleProvider.
- Provide explicit translator/negotiator factories, manifests, package tests and release gates.
- Retain authorization, settings, persistence, catalogue build/cache adapters and delivery in App.
- NRM-2026-006: enabling extraction only; App adoption requires a separately verified release.
