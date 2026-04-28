# SilverStripe Opauth Module (Community Fork)

> **⚠️ Note:** This is a community-maintained fork of the original [BetterBrief/silverstripe-opauth](https://github.com/BetterBrief/silverstripe-opauth), which has been archived. We've updated the module to work with newer SilverStripe versions, but it has not been extensively tested beyond our own use cases. **Use at your own risk.**

## Introduction

Uses the [Opauth](http://opauth.org) library for easy drop-in strategies for social login (Facebook, Twitter, Google, and more). Based on the identity data from the OAuth provider, the module will find or create a `Member` based on the provided email address.

## Status

- **Original project:** Archived, no longer maintained
- **This fork:** Updated for newer SilverStripe versions, limited testing
- **Contributions & bug reports:** Welcome! Please open an issue or submit a PR.

## How does it work?

The module provides an additional login form that allows users to sign in with an identity from any OAuth provider. Each provider is handled via an `OpauthStrategy` — strategies are available for Facebook, Twitter, Google, and [many more](http://opauth.org).

A `Member` can have multiple OAuth identities linked to a single account, saved as `OpauthIdentity` objects. If required data (like an email address) is missing from the provider's response, built-in validation lets you enforce required fields via `OpauthValidator`.

## Version Compatibility

| Branch | SilverStripe Version |
|--------|---------------------|
| `4.x`  | SilverStripe 4      |
| `5.x`  | SilverStripe 5      |
| `6.x`  | SilverStripe 6      |

## Requirements

- At least one [Opauth strategy](http://opauth.org)
- `allow_url_fopen` enabled in `php.ini` (recommended), or cURL as a fallback

## Installation

Install the branch matching your SilverStripe version:

```bash
composer require pixelpoems/silverstripe-opauth:dev-4.x   # for SilverStripe 4
composer require pixelpoems/silverstripe-opauth:dev-5.x   # for SilverStripe 5
composer require pixelpoems/silverstripe-opauth:dev-6.x   # for SilverStripe 6
```

## Quick Start

Add strategy configuration to your `_config.yml`:

```yaml
---
Name: silverstripe-opauth
After: 'framework/*','cms/*'
---
OpauthAuthenticator:
  opauth_settings:
    Strategy:
      Facebook:
        app_id: 'YOUR_APP_ID'
        app_secret: 'YOUR_APP_SECRET'
        scope: email
      Google:
        client_id: 'YOUR_CLIENT_ID'
        client_secret: 'YOUR_CLIENT_SECRET'
    security_salt: 'your-random-salt-here'
    security_iteration: 500
    security_timeout: '2 minutes'
    callback_transport: 'session'

OpauthIdentity:
  member_mapper:
    Facebook:
      FirstName: 'info.first_name'
      Surname: 'info.last_name'
      Email: 'info.email'
    Google:
      FirstName: 'info.first_name'
      Surname: 'info.last_name'
      Email: 'info.email'
```

For more detailed configuration options (including PHP-based config and custom member mapping), see the [original documentation](https://github.com/BetterBrief/silverstripe-opauth).

## What's included?

- **OpauthAuthenticator** — comparable to `MemberAuthenticator`
- **OpauthLoginForm** — configurable authentication options
- **OpauthRegisterForm** — optional intermediate step for incomplete profiles
- **OpauthController** — handles strategy communication
- **OpauthIdentity** — service-agnostic OAuth identity storage

## Contributing

Found a bug or want to help improve the module? Issues and pull requests are welcome. Since this fork has limited test coverage, real-world testing and feedback are especially valuable.

## License & Attribution

This fork is based on the original [silverstripe-opauth](https://github.com/BetterBrief/silverstripe-opauth) by Better Brief LLP, licensed under the BSD 3-Clause License. See [LICENSE](LICENSE) for details.

[Opauth](http://opauth.org) by U-Zyn Chua, available under MIT licence.
