# AI Agent Instructions for le-ssl-laravel-package

This document provides guidelines for AI agents (like Vibe CLI) working on the **SSL Manager** Laravel package.

## Project Overview

**Package Name:** `imagewize/ssl-manager`  
**Purpose:** A Laravel package for managing SSL certificates using Let's Encrypt  
**Repository:** https://github.com/imagewize/ssl-manager  
**License:** MIT (see LICENSE.txt)

## Project Structure

```
.
├── src/                    # Source code
│   └── Core/              # Core functionality
│       ├── HttpService.php
│       └── ...
├── composer.json          # Dependencies and package config
├── composer.lock          # Lock file
├── README.md              # User documentation
├── LICENSE.txt           # License file
└── CHANGELOG.md          # Version history
```

## Technical Stack

- **Framework:** Laravel (5.4+ through 12.0)
- **PHP Version:** 7.4+ (with PHP 8.4 compatibility)
- **Dependencies:**
  - `ext-curl`: *
  - `stonemax/acme2`: ^1.0
  - `league/flysystem`: ^1.1.5
- **Key Features:**
  - Let's Encrypt certificate generation
  - Automatic certificate renewal
  - Web server configuration generation
  - DNS verification (A records, CNAME)
  - Nginx configuration support

## Code Style & Conventions

### PHP
- Follows PSR-4 autoloading standard
- Type hints preferred (use `?array` for nullable arrays in PHP 8.4+)
- Method parameters: use nullable type hints (`?Type`) instead of default `null` where possible

### File Modifications
- When updating `composer.json`, verify Laravel version compatibility across the range
- PHP type hints should be explicitly nullable for optional parameters
- Always use `.md` extension for markdown files (not `.markdown`)
- Stage files explicitly with `git add <file>` before committing

## Common Tasks

### Adding Laravel Version Support
1. Update `composer.json` require section
2. Test with the new Laravel version
3. Update CHANGELOG.md with the change

### PHP Version Compatibility
- Use type hints compatible with PHP 7.4+
- For PHP 8.4+, use explicit nullable syntax (`?array` instead of `array = null`)

## Versioning

This project uses **Semantic Versioning**:
- **Patch (x.x.1):** Bug fixes, dependency updates, compatibility improvements
- **Minor (x.1.x):** Backwards-compatible new features
- **Major (1.x.x):** Breaking changes

Current version: **v1.3.1**

## Git Workflow

- **Main branch:** `master`
- **Feature branches:** Use descriptive names (e.g., `laravel-11`, `www-issue`)
- **Tags:** Version tags follow `vX.Y.Z` format (e.g., `v1.3`, `v1.2`)
- **Commits:** Use conventional commit messages (e.g., `fix(php84): ...`, `chore(composer): ...`)
- **Tooling:** Use `gh` (GitHub CLI) for PR operations when available (`gh pr create`, `gh pr view`, etc.)

## Testing

Note: This package may not have dedicated test files in the repository. Test changes by:
1. Installing the package in a Laravel application
2. Running the SSL certificate commands
3. Verifying web server configuration generation

## Important Files to Review

Before making changes, always check:
- `composer.json` - Laravel version compatibility
- `src/Core/HttpService.php` - Core certificate update logic
- `README.md` - User-facing documentation

## Branches in Use

- `master` - Stable releases
- `laravel-10` - Laravel 10 compatibility
- `laravel-11` - Laravel 11+ compatibility (current development)

## Special Notes

- The package was originally forked/renamed from another repository
- WWW/non-WWW domain handling has been a recent focus (see PRs #31, #32, #33)
- Certificate renewal (`renew` parameter) is a key feature
- Nginx configuration generation is a core component

## GitHub CLI (`gh`)

When working with GitHub repositories, use the `gh` CLI tool for streamlined operations:
- `gh pr create` - Create a pull request with pre-filled title/body
- `gh pr view` - View PR details in terminal
- `gh repo view` - Open repository in browser
- Always include file extensions (`.md`, `.php`, `.json`) when creating new files

## For AI Agents

When working on this repository:
1. **Read first:** Always read the file you're modifying before editing
2. **Minimal changes:** Make the smallest change that solves the problem
3. **Style matching:** Match existing indentation, naming, and error handling
4. **Laravel compatibility:** Ensure changes work across all supported Laravel versions
5. **Document changes:** Update CHANGELOG.md for any user-facing changes
6. **Commit messages:** Use conventional commit format with scope if applicable
7. **File extensions:** Always use `.md` for markdown files
