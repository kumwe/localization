# Releases and verification

The newest second-level SemVer heading in CHANGELOG.md is the release record. A leading `Unreleased`
heading is allowed. Malformed headings fail closed; the current and tagged changelogs use the same parser.
0.1.0 is the initial implementation release record, not a claim that a tag or registry version exists.

Pull requests run `composer check` on PHP 8.5 with ext-intl/zip. The main-only release-on-record workflow
reruns the complete matrix, serializes release jobs without cancellation, and creates the missing version
tag at the tested merged SHA through the GitHub API. An existing tag/release is verified and not mutated;
only an explicit HTTP 404 permits creation. Maintainers merge; implementation agents do not merge, tag,
publish, enable auto-merge or submit Packagist. The initial Packagist submission belongs to the maintainer.

The release archive includes LICENSE, the full charter/API/service/capability documentation, examples,
the handoff and production sources. Tests, fixtures, vendor, tools and workflow state are excluded.
The clean-consumer gate installs this exact ZIP as a dependency in a fresh Composer project with no-dev
authoritative classmaps. It loads every manifested type through the consumer autoloader and exercises
both standalone translation and a real host-installed ServiceManager, including alias and lifetime rules.
It must never install the archive as the consumer root, since that hides dependency-relative autoload bugs.

PHP/ICU versions affect locale-sensitive data. Record the actual PHP/ICU/platform versions in independent
release evidence. Maintainers support the tested PHP 8.5 Linux source install lane; other platforms require
their own evidence before support is claimed. No fixed ICU version is embedded into this portable package.

After publication, a separate fresh verification session checks exact tag/source/archive/manifests,
registry install, license/security and consumer results and records RELEASE-ATTESTATION.yaml outside the
artifact it hashes. No future merge SHA, archive checksum or registry availability is invented in the
handoff. App adoption is blocked until verification succeeds. Pre-1.0 consumers pin exact versions.

Behavior changes require regression vectors, a reviewed changelog record and SemVer classification.
Rollback restores the previous application/Composer lock and compatible PHP/ICU deployment together.
Security corrections follow the reporting policy and receive an immutable successor release.
