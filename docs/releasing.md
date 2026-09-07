# Releases and verification

The newest second-level SemVer heading in CHANGELOG.md is the release record. A leading `Unreleased`
heading is allowed. Malformed headings fail closed; the current and tagged changelogs use the same parser.
0.1.1 is the successor release record; 0.1.0 remains an unchanged historical publication.

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

## Maintainer setup before merging the 0.1.1 successor

1. Protect main with an active branch protection rule or ruleset requiring the reviewed pull request and
   package CI. The release job reads GitHub's ref_protected event context and refuses false or missing state
   before any tag or release mutation. This does not configure or alter repository permissions.
2. Enable immutable releases in the repository release settings before merging. GitHub applies that option
   to future releases; it does not retroactively protect 0.1.0. Keep the existing tag and release intact.
3. Review and merge the 0.1.1 changelog record. The existing release lane re-proves every package check,
   verifies tag ancestry and release-record identity, then publishes using its ordinary workflow token.
4. Require the final metadata check to pass: the exact version must be published, stable and immutable.
   The same check runs when an existing release is found. Mutable, draft, missing or contradictory metadata
   fails closed. If immutable releases were not enabled first, a failed post-publication check cannot undo
   publication; leave that version intact and prepare another reviewed successor.
5. Obtain a fresh independent RELEASE-ATTESTATION.yaml for the successor before a dependent package or App
   adopts it. A green package PR or a failed publication workflow is not release verification.

The workflow does not call the administrative immutable-releases settings endpoint or use an admin PAT.
Configuration is a maintainer action; publication verification uses the normal release metadata endpoint.
The development gate uses Bash and jq with isolated response fixtures. These tests exercise refusal logic
without contacting GitHub or claiming that protections are currently configured. Both helper scripts are
under the export-ignored tools directory, so the runtime archive and runtime dependency ceiling are unchanged.

All portable implementation tests remain package-owned. The handoff's exact App integration/security test
retention and later duplicate-test removal instructions are unchanged by this release-only correction.
