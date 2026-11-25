# Security Policy

## Supported versions

| Version | Supported |
|---------|-----------|
| 1.x     | Yes       |

## Reporting a vulnerability

Please **do not** open a public issue for security problems.

Report them privately using GitHub's
[private vulnerability reporting](https://github.com/JustRohams/LB-Creator/security/advisories/new)
for this repository, and include:

- A description of the issue and its impact
- Steps to reproduce
- Affected version(s)
- A suggested fix, if you have one

You can expect an initial response within a few days. Once the issue is confirmed, a fix is
prepared and released, and you will be credited if you wish.

## Notes for server owners

- LBCreator uses reflection to call getter methods of other plugins. Only grant the
  `lbcreator.*` permissions to trusted staff (they default to `op`).
- Keep the `backups/` folder private; it contains your leaderboard configuration.
