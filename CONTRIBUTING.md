# Contributing to LBCreator

Thanks for your interest in improving LBCreator! Contributions of all kinds are welcome.

## Reporting bugs

Open an issue and include:

- PocketMine-MP version and PHP version
- LBCreator version
- Steps to reproduce the problem
- The relevant part of the server log or crash dump
- Your `config.yml` if it is related

## Suggesting features

Open an issue describing the use case first, so we can discuss it before any code is written.

## Pull requests

1. Fork the repository and create a branch from `main`
   (`feature/<name>` or `fix/<name>`).
2. Make your change, keeping each pull request focused on one topic.
3. Test it on a real PocketMine-MP (API 5) server.
4. Update `CHANGELOG.md` under **Unreleased**.
5. Open the pull request and describe what changed and why.

## Code style

- PHP 8.1+, `declare(strict_types=1);` in every file
- PSR-4: **the file name must match the class name** inside `src/RohaM/LBCreator/`
- Type-hint parameters and return values; use `?Type` instead of implicit nullable defaults
- Cast values to `string` before passing them to string functions (strict types are on)
- Build forms from plain arrays — LBCreator has no form library dependency
- Keep user-facing text in `resources/messages.yml`

## Commit messages

Use short, imperative messages, for example `Fix floating text not despawning on delete`.

## License

By contributing you agree that your work is released under the [MIT License](LICENSE).
