# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.3.2] - 2026-10-01

### Added
- `LICENSE.md` (MIT) with the correct copyright holders; it replaces `LICENSE.txt`, which carried the copyright line of an unrelated project
- `composer.json` description, keywords, homepage and Imagewize as author

### Changed
- `README.md` rewritten: what the package does, how it works, installation (including publishing the config and view to the right paths), all config options and the commands. It no longer talks about a private repository
- Default for `root_site` in `config/ssl-manager.php` is `/var/www/your-app/current/public` instead of a Smart48 server path (apps set `SSL_ROOT_SITE` anyway)
- The repository moved from `smart48/le-ssl-laravel-package` to `imagewize/ssl-manager` (GitHub redirects the old URL)

## [v1.3.1] - 2026-09-30

### Added
- Added `AGENTS.md` with AI agent instructions for repository

### Changed
- Updated Laravel framework support to `^10.0 || ^11.0 || ^12.0` in composer.json
- Made `certificateInfo` parameter in `updateSite` method explicitly nullable with `?array` type hint for PHP 8.4 compatibility

### Removed
- Removed stale `composer.lock` (pinned vulnerable Laravel 6 / Symfony 4.4 packages); libraries should not ship a lockfile

## [v1.3] - 2023-04-19

### Added
- Usage notes for `renew` and `update` commands
- Documentation for command parameters including `true` parameter usage
- Style typo fixes

### Changed
- Repository name updated
- Passwordless nginx restart for web user
- Credit additions in documentation

## [v1.2] - 2022-12-29

### Changed
- DHparam location change
- Different FastCGI unix socket location support
- Added `$realpath_root` support

## [v1.1.1] - 2022-10-21

### Added
- PHP 8.1 support

### Changed
- Failed notification improvements
- Update retries changed to 1
- PHP 8.0 support
- Laravel 9 support

## [v1.1] - 2021-12-16

### Changed
- Updated league/flysystem from 1.0.44 to 1.1.5
- Updated laravel/framework from 5.4.36 to 6.20.26

## [v1.0] - 2020-11-24

### Changed
- PHP 7.4 FPM support

## [0.1.1] - 2020-05-04

### Fixed
- Domain name as a challenge catalog not exist error

### Added
- Order expiry info message
- Renew parameter improvements
- Improved info messages
- Web server reload reordering
- IPv6 support in custom sites nginx config template
- Instant certificate update command without queue
- `renew` argument to update-certificate command

### Changed
- Disabled use of staging Let'sEncrypt service
- Fixed typo in SslService
- Changed permissions of created catalogs to 755
- Fixed DNS resolve issue
- Fixed catalogs "not exists" issues when generating config
- Improved intro and documentation

## [0.1.0] - 2018-03-15

### Added
- Initial project setup
- README with CNAME reference
- Composer file configuration
- Fixed CNAME verification logic
- Initial SSL certificate management functionality

[v1.3.1]: https://github.com/imagewize/ssl-manager/compare/v1.3...v1.3.1
[v1.3]: https://github.com/imagewize/ssl-manager/compare/v1.2...v1.3
[v1.2]: https://github.com/imagewize/ssl-manager/compare/v1.1.1...v1.2
[v1.1.1]: https://github.com/imagewize/ssl-manager/compare/v1.1...v1.1.1
[v1.1]: https://github.com/imagewize/ssl-manager/compare/v1.0...v1.1
[v1.0]: https://github.com/imagewize/ssl-manager/compare/0.1.1...v1.0
[0.1.1]: https://github.com/imagewize/ssl-manager/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/imagewize/ssl-manager/commits/0.1.0
