# Changelog

## [0.1.0-beta.3](https://github.com/duonrun/cms/releases/tag/0.1.0-beta.3) (2026-04-22)

- Added a new `update-panel-path` admin command that extracts and duplicates the panel path rewrite logic from `install-panel`.
- `update-panel-path` now renames `public/cms` to the configured `path.panel` only when the configured panel directory does not exist and the default directory exists.
- If the configured panel directory already exists, `update-panel-path` only updates file references and does not remove or replace that directory.
- Added clearer error handling when neither the configured panel directory nor the default `public/cms` directory exists.

## [0.1.0-beta.2](https://github.com/duonrun/cms/releases/tag/0.1.0-beta.2) (2026-02-01)

Codename: Benjamin

- Added support for installing the panel from tagged releases (including alpha/beta/rc), instead of only nightly builds.
- Improved the `install-panel` command output and removed the unnecessary Quma command dependency.
- Updated the panel release workflow to support prerelease tag patterns and manual (retroactive) runs.

## [0.1.0-beta.1](https://github.com/duonrun/cms/releases/tag/0.1.0-beta.1) (2026-02-01)

Initial release - Codename: Sabine
