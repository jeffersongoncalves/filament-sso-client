<div class="filament-hidden">

![Filament SSO Client](https://raw.githubusercontent.com/jeffersongoncalves/filament-sso-client/3.x/art/jeffersongoncalves-filament-sso-client.png)

</div>

# Filament SSO Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/filament-sso-client.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/filament-sso-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/filament-sso-client/tests.yml?branch=3.x&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/filament-sso-client/actions?query=workflow%3ATests+branch%3A3.x)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/filament-sso-client/pint.yml?branch=3.x&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/filament-sso-client/actions?query=workflow%3Apint+branch%3A3.x)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/filament-sso-client.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/filament-sso-client)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/filament-sso-client.svg?style=flat-square)](LICENSE.md)

Filament panel integration for [`jeffersongoncalves/laravel-sso-client`](https://github.com/jeffersongoncalves/laravel-sso-client): a "Sign in with SSO" button on the panel login page (or a direct redirect to the SSO Server), federated logout from the user menu and Single Logout enforcement on every panel request.

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| [1.x](https://github.com/jeffersongoncalves/filament-sso-client/tree/1.x) | 3.x |
| [2.x](https://github.com/jeffersongoncalves/filament-sso-client/tree/2.x) | 4.x |
| [3.x](https://github.com/jeffersongoncalves/filament-sso-client/tree/3.x) | 5.x |

Requires `laravel-sso-client` 1.1+ (federated logout).

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/filament-sso-client
```

Then configure `laravel-sso-client` as described in [its README](https://github.com/jeffersongoncalves/laravel-sso-client#installation) (server credentials, `sso_id` migration).

The panel and the SSO callback must log users into the same guard: keep `sso-client.guard` equal to the panel's `authGuard()` (both default to the app's default guard).

## Usage

Register the plugin in your `PanelProvider`:

```php
use JeffersonGoncalves\Filament\SsoClient\SsoClientPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->plugins([
            SsoClientPlugin::make(),
        ]);
}
```

The plugin:

- replaces the panel login page with one that shows a **Sign in with SSO** button under the local email/password form;
- points the user menu **logout** item at `POST /sso/logout`, which ends the local session, the SSO session and the user's other client apps;
- adds a panel auth middleware that ends sessions revoked by Single Logout (the `sso.auth` check, for signed-in users only).

Filament's own `Authenticate` middleware stays in place, so `canAccessPanel()` is still enforced for SSO users.

### SSO only (redirect straight to the SSO Server)

```php
SsoClientPlugin::make()
    ->forceRedirect(),
```

Guests that open the panel go directly to the SSO Server and come back to the page they asked for.

### SSO button only (no local form)

```php
SsoClientPlugin::make()
    ->loginForm(false),
```

The login page only shows the SSO button, and local credentials are refused.

### Button label

```php
SsoClientPlugin::make()
    ->buttonLabel('Sign in with your corporate account'),
```

The default label is translated (`en`, `pt_BR`); publish the translations with `php artisan vendor:publish --tag="filament-sso-client-translations"`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email the author instead of using the issue tracker.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
