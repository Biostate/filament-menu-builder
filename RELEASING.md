# Releasing

This package is consumed via Composer / Packagist, so a release is "a git tag pushed to GitHub." Packagist auto-imports tagged versions; a GitHub Release surfaces the changelog on the repo page.

## Versioning

We follow [SemVer](https://semver.org/):

- **Patch** (`5.0.x`) — backwards-compatible bug fixes, internal refactors, CI / tooling.
- **Minor** (`5.x.0`) — backwards-compatible additions; soft API changes that shouldn't break working installs but are worth flagging in `UPGRADING.md` if it exists.
- **Major** (`x.0.0`) — breaking changes to the public API.

When in doubt, prefer the larger bump.

## Cutting a release

1. Confirm `main` is green: latest commit on `main` should have all required checks passing (`gh pr checks` or the Actions tab).
2. Open `CHANGELOG.md` and convert the `## Unreleased` heading to `## vX.Y.Z - YYYY-MM-DD`. Append a `**Full Changelog**` link comparing against the previous tag.
3. Commit on `main`:
   ```
   git commit -m "Update CHANGELOG for vX.Y.Z"
   git push origin main
   ```
4. Create an annotated tag:
   ```
   git tag -a vX.Y.Z -m "vX.Y.Z release notes..."
   git push origin vX.Y.Z
   ```
5. Create a GitHub Release on top of the tag (this notifies watchers and is what shows up on the Releases page):
   ```
   gh release create vX.Y.Z \
     --title "vX.Y.Z" \
     --notes "<release notes — usually the same content as the CHANGELOG entry>"
   ```
6. Within a few minutes, Packagist picks up the new tag automatically. Verify at <https://packagist.org/packages/biostate/filament-menu-builder>.

## Merging contributor PRs

We use **merge commits** (not squash, not rebase) so each contributor's history stays attributable. Use `gh pr merge <number> --merge --delete-branch` or the equivalent GitHub UI option.

## Hotfixes

If a critical issue ships, branch off the latest tag (`git checkout -b hotfix/foo vX.Y.Z`), apply the fix, open a PR against `main`, and follow the normal release flow with a patch bump.
