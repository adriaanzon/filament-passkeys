# Passkey MFA for Filament

A Filament v5 panel plugin that adds passkey/WebAuthn as a multi-factor authentication method. Users can register one or more passkeys (fingerprint, face, device PIN, security key) from the profile page and use them as the second factor when signing in. Built on top of [`laravel/passkeys`][laravel-passkeys].

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

4. Register the plugin on your panel and add `PasskeyAuthentication` to your panel's multi-factor authentication providers:
   ```php
   use AdriaanZon\FilamentPasskeys\Auth\PasskeyAuthentication;
   use AdriaanZon\FilamentPasskeys\FilamentPasskeysPlugin;

   public function panel(Panel $panel): Panel
   {
       return $panel
           // ...
           ->plugin(FilamentPasskeysPlugin::make())
           ->multiFactorAuthentication([
               PasskeyAuthentication::make(),
           ]);
   }
   ```

## Configuration

WebAuthn settings (relying party ID, allowed origins, user handle secret, timeout) live in [`laravel/passkeys`][laravel-passkeys]'s config. Publish it with:

```bash
php artisan vendor:publish --tag="passkeys-config"
```

To customise the label rendered next to the passkey provider on the MFA challenge form, use the plugin's `loginFormLabel()` builder method:

```php
FilamentPasskeysPlugin::make()->loginFormLabel('Sign in with a passkey')
```

A `Closure` is also accepted, evaluated at render time:

```php
FilamentPasskeysPlugin::make()->loginFormLabel(fn () => __('auth.passkey_label'))
```

## Roadmap

Passkey as primary authentication (passwordless sign-in) is planned for a future release. The current release covers passkey MFA only.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

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
