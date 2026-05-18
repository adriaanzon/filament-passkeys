<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys;

use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyConfirmationController;
use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyLoginController;
use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyRegistrationController;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Route;

class FilamentPasskeysPlugin implements Plugin
{
    protected bool $passwordlessLogin = false;

    public function getId(): string
    {
        return 'filament-passkeys';
    }

    public function register(Panel $panel): void
    {
        $throttle = (array) config('passkeys.throttle');

        $panel->authenticatedRoutes(function () use ($throttle): void {
            Route::prefix('user/passkeys')->middleware($throttle)->group(function (): void {
                Route::get('options', [PasskeyRegistrationController::class, 'index'])
                    ->name('passkey.registration-options');
                Route::post('/', [PasskeyRegistrationController::class, 'store'])
                    ->name('passkey.store');
            });
        });

        $panel->routes(function () use ($throttle): void {
            Route::prefix('passkeys/confirm')->middleware($throttle)->group(function (): void {
                Route::get('options', [PasskeyConfirmationController::class, 'index'])
                    ->name('passkey.confirm-options');
                Route::post('/', [PasskeyConfirmationController::class, 'store'])
                    ->name('passkey.confirm');
            });
        });

        if ($this->passwordlessLogin) {
            $panel->routes(function () use ($throttle): void {
                Route::prefix('passkeys/login')
                    ->middleware($throttle)
                    ->group(function (): void {
                        Route::get('options', [PasskeyLoginController::class, 'index'])
                            ->name('passkey.login-options');
                        Route::post('/', [PasskeyLoginController::class, 'store'])
                            ->name('passkey.login');
                    });
            });
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function passwordlessLogin(bool $enabled = true): static
    {
        $this->passwordlessLogin = $enabled;

        return $this;
    }

    public function hasPasswordlessLogin(): bool
    {
        return $this->passwordlessLogin;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
