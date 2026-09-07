# Releasing Localization

Follow the [Package release standard](package-release-standard.md) for the shared
quality gate, changelog parsing, publication and retry behavior. Complete the
[repository release setup](repository-release-setup.md) with an administrator
session before merging a release record:

```bash
bash tools/configure-release-repositories.sh --check kumwe/localization
bash tools/configure-release-repositories.sh --apply kumwe/localization
```

The required CI check is **Package gate**. Maintainers rebase reviewed PRs into the
repository's current default branch; the release workflow reruns the same quality
gate on the resulting commit and derives its release identity from that run.
A release intention in CHANGELOG.md is not evidence that publication occurred.
Keep work that is not ready for publication under `## Unreleased`.

## Artifact, platform and consumer verification

The archive includes LICENSE, the full charter/API/service/capability documentation,
examples, the handoff and production sources. Tests, fixtures, vendor, tools and
workflow state are excluded. The clean-consumer gate installs this exact ZIP as a
dependency in a fresh Composer project with no-dev authoritative classmaps. It loads
every manifested type through the consumer autoloader and exercises standalone
translation and a real host-installed ServiceManager, including alias and lifetime
rules. Installing the archive as the consumer root would conceal dependency-relative
autoload defects and does not satisfy this gate.

The supported source-install lane is PHP 8.5 on Linux with the declared extensions.
PHP and ICU versions affect locale-sensitive data: record the actual PHP, ICU and
platform versions in independent evidence. Other platforms require their own proof
before support is claimed. The portable package does not embed a fixed ICU version.

Behavior changes need regression vectors, a reviewed changelog and SemVer
classification; pre-1.0 consumers pin exact versions. Rollback restores the previous
application/Composer lock and compatible PHP/ICU deployment together. Security
corrections follow the reporting policy and receive an immutable successor.
Portable tests remain package-owned; App test retention follows the handoff.

## Publication evidence and recovery

The maintainer performs the initial Packagist submission. Its GitHub integration
then follows tags without a registry credential in CI. Before dependent publication
or App adoption, a fresh independent verifier must bind the exact published
source/tag, archive digest, manifests, registry coordinate, license/security and
clean-consumer results in an external RELEASE-ATTESTATION.yaml. The artifact and
handoff must not invent their own final commit, checksum or publication evidence.

Use the current release workflow on the default branch to retry after correcting
repository settings. Historical mutable releases remain unchanged: enabling
immutability affects future publications, so a mutable version requires an unused
successor. Never move or delete a published tag or replace a released artifact.
An unpublished tag can be completed only on the exact commit tested by the retry.
A green PR does not replace the default-branch release result or independent
verification. Administrator credentials do not belong in Actions.
