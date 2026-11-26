# LBCreator

[![PocketMine-MP](https://img.shields.io/badge/PocketMine--MP-API%205-blue)](https://pmmp.io)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4)](https://www.php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**LBCreator** is a dynamic leaderboard creator for [PocketMine-MP](https://pmmp.io) (API 5).
It scans another plugin for its statistic getters and turns any of them into a floating-text
leaderboard, without writing a single line of code.

> Repository: <https://github.com/JustRohams/LeaderBoard-Creator>

## Features

- Create a leaderboard for **any enabled plugin** — statistics are discovered automatically via reflection
- Form-based menu (`/lbc`) and full sub-command support
- Floating text leaderboards that refresh automatically
- Configurable title, end text, player limit (1–50), colours and layout
- YAML or JSON storage, with automatic rolling backups
- Result caching to keep the server fast
- Developer events: create, edit, delete, teleport and update
- No external libraries required

## Requirements

| Requirement | Version |
|-------------|---------|
| PocketMine-MP | API 5.0.0+ |
| PHP | 8.1+ |

## Installation

1. Download the latest `.phar` from the releases page (or build it from source with DevTools).
2. Put it in your server's `plugins/` folder.
3. Restart the server.

## Commands

All commands are player-only.

| Command | Description | Permission |
|---------|-------------|------------|
| `/lbc` | Open the main menu | `lbcreator.command` |
| `/lbc create [plugin] [stat] [limit]` | Create a leaderboard at your position | `lbcreator.create` |
| `/lbc delete <id>` | Delete a leaderboard | `lbcreator.delete` |
| `/lbc teleport <id>` | Move a leaderboard to your position | `lbcreator.teleport` |
| `/lbc edit <id> [limit\|title\|end\|stat] [value]` | Edit a leaderboard (opens a form if no key is given) | `lbcreator.edit` |
| `/lbc list` | List all leaderboards | `lbcreator.list` |
| `/lbc reload` | Reload config and messages | `lbcreator.reload` |
| `/lbc help` | Show help | `lbcreator.help` |

All permissions default to `op`.

### Example

```
/lbc create MyPlugin kills 10
/lbc edit 1 title Top Killers
/lbc teleport 1
```

## Configuration

`plugin_data/LBCreator/config.yml`

| Key | Default | Description |
|-----|---------|-------------|
| `update_interval` | `5` | Seconds between leaderboard refreshes |
| `default_limit` | `10` | Default number of players shown |
| `default_title` | `"RohaM"` | Default leaderboard title |
| `default_end_text` | `""` | Default footer text |
| `cache_time` | `60` | Seconds a fetched result stays cached |
| `async_enabled` | `true` | Use the deferred update task |
| `rank_colors` | gold / grey / dark grey | Colour per rank (`1`, `2`, `3`, `default`) |
| `database.type` | `yaml` | `yaml` or `json` |
| `database.backup` | `true` | Enable automatic backups |
| `database.backup_interval` | `3600` | Seconds between backups |
| `display.*` | — | Separator, rank prefix, value prefix/suffix, max name length |
| `scanner.max_depth` | `3` | Depth of the reflection scan |
| `scanner.exclude_methods` | — | Getter names ignored by the scanner |

Messages can be customised in `messages.yml`.

## Data files

```
plugin_data/LBCreator/
├── config.yml
├── messages.yml
├── leaderboards.yml        # all leaderboards (or leaderboards.json)
├── leaderboards_data/      # latest top-N snapshot per leaderboard
└── backups/                # automatic backups (last 5 are kept)
```

## How it works

LBCreator inspects the target plugin (and the objects stored in its properties) for public
`getSomething()` style methods. Each one becomes a selectable *statistic*. On every refresh it
calls the matching getter for each **online** player, sorts the values and renders the top N.

### Limitations

- Only players that are **currently online** are ranked.
- A statistic is only found if the target plugin exposes it through a getter that takes no
  argument or a single `Player` argument and returns a number (or a simple container of one).

## Developer API

```php
use RohaM\LBCreator\event\LeaderboardCreateEvent;

public function onCreate(LeaderboardCreateEvent $event): void {
    $id = $event->getLeaderboardId();
}
```

Available events: `LeaderboardCreateEvent`, `LeaderboardEditEvent`, `LeaderboardDeleteEvent`,
`LeaderboardTeleportEvent`, `LeaderboardUpdateEvent`.

## Contributing

Pull requests are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md).
Please read the [Code of Conduct](CODE_OF_CONDUCT.md) and report vulnerabilities as described
in [SECURITY.md](SECURITY.md).

## License

Released under the [MIT License](LICENSE).

Made by **RohaM** / **Cyrinex**.
