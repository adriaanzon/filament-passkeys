# Filament Passkeys

Filament v5 panel plugin that adds passkey/WebAuthn MFA via `laravel/passkeys`.

## Three-package ecosystem

These are easy to confuse — get them straight:

- **`laravel/passkeys`** (Packagist) — GitHub repo: `laravel/passkeys-server`. Server-side Composer package. Ships migration, `PasskeyAuthenticatable` trait, `PasskeyUser` contract, WebAuthn verification via action classes (`GenerateRegistrationOptions`, `StorePasskey`, `GenerateVerificationOptions`, `VerifyPasskey`).
- **`@laravel/passkeys`** (npm) — GitHub repo: `laravel/passkeys`. Browser-side JS helper. Exports a `Passkeys` object (not individual functions). API: `Passkeys.register({ name, routes? })`, `Passkeys.verify({ routes? })`, `Passkeys.isSupported()`. Both `register()` and `verify()` do a full round-trip (GET options → browser ceremony → POST result). Route overrides via `{ routes: { options: '...', submit: '...' } }` are required when using non-default endpoint paths.
- **`laravel/fortify`** — wraps the server package with routes/middleware. This plugin does the same thing Fortify does, but for Filament panels.

## Rules

- **Verify against source code.** Do not guess how Filament internals, `laravel/passkeys`, or `@laravel/passkeys` work. Read the actual source before making claims or writing code that depends on their APIs.
- **Run `composer analyse` before claiming work is complete. PHPStan must pass.** Fix the root cause — no `@phpstan-ignore`, baseline entries, type widening, or casts just to silence it.
