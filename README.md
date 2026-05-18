# Passkeys for Filament

A Filament v5 panel plugin for passkey/WebAuthn authentication. Use passkeys as a second factor, as a passwordless sign-in option on the login page, or both. Users register passkeys (fingerprint, face, device PIN, security key) from their profile page. Built on top of [`laravel/passkeys`][laravel-passkeys].

## Installation

1. Install the package via Composer:
   ```bash
   composer require adriaanzon/filament-passkeys
   ```

2. Publish and run the [`laravel/passkeys`][laravel-passkeys] migration:
   ```bash
   php artisan vendor:publish --tag="passkeys-migrations"
   php artisan migrate
   ```

3. Add the `PasskeyUser` contract and `PasskeyAuthenticatable` trait to your user model:
   ```php
   use Filament\Models\Contracts\FilamentUser;
   use Illuminate\Foundation\Auth\User as Authenticatable;
   use Laravel\Passkeys\Contracts\PasskeyUser;
   use Laravel\Passkeys\PasskeyAuthenticatable;

   class User extends Authenticatable implements FilamentUser, PasskeyUser
   {
       use PasskeyAuthenticatable;

       // ...
   }
   ```

4. Register the plugin on your panel. See [Configuration](#configuration) for all available modes.
   ```php
   use AdriaanZon\FilamentPasskeys\FilamentPasskeysPlugin;
   use AdriaanZon\FilamentPasskeys\PasskeyAuthentication;

   public function panel(Panel $panel): Panel
   {
       return $panel
           ->login()
           ->profile()
           // ...
           ->plugins([
               FilamentPasskeysPlugin::make()->passwordlessLogin(),
           ])
           ->multiFactorAuthentication([
               PasskeyAuthentication::make()->managementOnly(),
           ]);
   }
   ```

## Configuration

This plugin supports the following setups:

- [Passwordless sign-in only](#passwordless-sign-in-only)
- [Passkey MFA](#passkey-mfa)
- [Passwordless sign-in + passkey MFA](#passwordless-sign-in--passkey-mfa)

### Passwordless sign-in only

A "Sign in with passkey" button + browser autofill on the login page, with passkeys excluded from Filament's MFA challenge.

Users can register and manage passkeys from their profile page. The `->managementOnly()` setting keeps that UI intact while skipping the MFA challenge step.

```
->plugins([
    FilamentPasskeysPlugin::make()->passwordlessLogin(),
])
->multiFactorAuthentication([
    PasskeyAuthentication::make()->managementOnly(),
]);
```

### Passkey MFA

If you'd rather use passkeys as a second factor on top of password login (and not enable passwordless sign-in), drop both `->passwordlessLogin()` and `->managementOnly()`:

```php
->plugins([
    FilamentPasskeysPlugin::make(),
])
->multiFactorAuthentication([
    PasskeyAuthentication::make(),
])
```

#### Fallback MFA method

Without a usable passkey, users cannot get past the MFA challenge and would be locked out. Pair `PasskeyAuthentication` with one of Filament's built-in providers like [`EmailAuthentication`](https://filamentphp.com/docs/5.x/users/multi-factor-authentication#email-authentication) so they can still sign in:

```php
use Filament\Auth\MultiFactor\Email\EmailAuthentication;

->multiFactorAuthentication([
    PasskeyAuthentication::make(),
    EmailAuthentication::make(),
])
```

### Passwordless sign-in + passkey MFA

To use passkeys as both a login option and an MFA factor for password sign-ins, enable passwordless sign-in *without* `->managementOnly()`:

```php
->plugins([
    FilamentPasskeysPlugin::make()->passwordlessLogin(),
])
->multiFactorAuthentication([
    PasskeyAuthentication::make(),
])
```

## WebAuthn configuration

WebAuthn settings (relying party ID, allowed origins, user handle secret, timeout, throttling) live in [`laravel/passkeys`][laravel-passkeys]'s config. Publish it with:

```bash
php artisan vendor:publish --tag="passkeys-config"
```

The `passkeys.throttle` value is applied to every passkey endpoint this plugin registers (defaults to `throttle:6,1`).

## Changelog

Please see the [releases](https://github.com/adriaanzon/filament-passkeys/releases) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Adriaan Zonnenberg](https://github.com/adriaanzon)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[laravel-passkeys]: https://github.com/laravel/passkeys-server
