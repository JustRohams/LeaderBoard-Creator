# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Fixed
- Class files whose names did not match their class (`LBCCommand`, `ListLeaderboardsForm`,
  `DeleteSubCommand`), which broke autoloading.
- Forms: missing `send()` method and use of form element classes that do not exist in
  PocketMine-MP core; forms are now built from plain arrays.
- `ConfirmationForm`: invalid `callable` property type and wrong modal response handling.
- `EditLeaderboardForm`: the stat dropdown index is now mapped to the stat name.
- `ListSubCommand`: syntax error (unclosed parenthesis).
- `CreateSubCommand`: missing `SelectStatForm` import.
- `LanguageManager`: missing `Config` import.
- `Messages`: `TypeError` under `strict_types` when replacing placeholders with numbers.
- `FloatingTextHelper`: old floating text was never despawned on delete, edit or teleport.
- `DynamicDataFetcher` / `PluginScanner`: fatal error when a plugin has union-typed properties.
- `TextFormatter`: `TypeError` for purely numeric player names.
- `LeaderboardManager`: stale data files after delete or stat change, implicit nullable
  parameter, and missing validation when editing limit or stat.
- `/lbc edit` now accepts multi-word titles and end text.

### Added
- `LICENSE`, `README.md`, `README.fa.md`, `CHANGELOG.md`, `CONTRIBUTING.md`,
  `CODE_OF_CONDUCT.md` and `SECURITY.md`.

## [1.0.0]

### Added
- Initial release: dynamic leaderboard creation for any plugin, form menu and sub-commands,
  floating text display, YAML/JSON storage, backups, caching and developer events.
